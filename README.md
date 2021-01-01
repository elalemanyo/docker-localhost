# Docker localhost
This project provides a basic Docker setup, for building a local development environment with HTTPS support.


##  Requirements
- [Docker](https://www.docker.com)
- Docker Compose, Docker App and Docker Toolbox already include Compose along with other Docker apps, so most users do not need to install Compose separately.
- [mkcert](https://github.com/FiloSottile/mkcert)

## Features
- [Træfik](https://traefik.io) HTTP reverse proxy and load balancer made to deploy microservices with ease.
- [Portainer](https://portainer.io/) Simple management UI for Docker.

## Installation / run
Generate certificates using mkcert:

```bash
# If it's the firt install of mkcert, run
mkcert -install

# Generate certificate for domain "d.test", "d.uno" and their sub-domains
mkcert -cert-file certs/local-cert.pem -key-file certs/local-key.pem "d.test" "*.d.test" "d.uno" "*.d.uno"
```

Now, start containers with :

```bash
docker-compose up -d
```

## Hosts File - Wildcard DNS domain on Mac OS X
Using [Dnsmasq](http://www.thekelleys.org.uk/dnsmasq/doc.html) as a local resolver.

### Install Dnsmasq
Install it with brew

```bash
brew install dnsmasq
```

Create the etc dir if needed

```bash
mkdir -p /usr/local/etc
```

Create a simple configuration, where all .dev domains would respond with 127.0.0.1

```bash
echo "address=/.test/127.0.0.1" > /usr/local/etc/dnsmasq.conf
```

Install the daemon startup file

```bash
sudo cp -fv /usr/local/opt/dnsmasq/*.plist /Library/LaunchDaemons
```

Start the daemon

```bash
sudo launchctl load /Library/LaunchDaemons/homebrew.mxcl.dnsmasq.plist
```

All we need to do is tell the resolver to use Dnsmasq for .test domains:

```bash
# man 5 resolver
sudo mkdir -p /etc/resolver
sudo sh -c 'echo "nameserver 127.0.0.1" > /etc/resolver/test'
```

Now you can now use any .test domain and it will always resolve to 127.0.0.1.<br/>
You can easily create new domains on the fly, and never have to worry about your /etc/hosts file again.

**Source:** [Setting up a wildcard DNS domain on Mac OS X](http://asciithoughts.com/posts/2014/02/23/setting-up-a-wildcard-dns-domain-on-mac-os-x/) - [ASCII Thoughts](http://asciithoughts.com)

**Warning:** [Chrome & Firefox now force .dev domains to HTTPS via preloaded HSTS](https://ma.ttias.be/chrome-force-dev-domains-https-via-preloaded-hsts/)

## Accessing services

- [Træfik](https://traefik.d.test)
- [Portainer](https://portainer.d.test)

## Setting a new Project
Add Træfik labels in container from docker-comose.yml:

```
labels:
  - "traefik.enable=true"
  - "traefik.http.routers.${PROJECT_NAME}.tls=true"
  - "traefik.http.routers.${PROJECT_NAME}.rule=Host(`${PROJECT_NAME}.d.test`)"
  - "traefik.http.services.${PROJECT_NAME}.loadbalancer.server.port=80"
```

And add at the end the network:

```
networks:
  frontend:
    external:
      name: docker-localhost_frontend
```

### Xdebug

Making Xdebug working with Docker is quite tricky as there is currently a limitation to Docker for Mac that prevents a container to make a request to the the host, which is exactly what we would like to do with Xdebug.

To make Xdebug debugging work, you will first need to run this command on the host:

	sudo ifconfig lo0 alias 10.200.10.1/24

This IP address is configured in the environment variables of the PHP container, in the `docker-compose.yml` file:

    services:
      php:
        # ...
        environment:
          XDEBUG_REMOTE_HOST: 10.200.10.1

Or add it to the  `php.ini`:

```php
[Xdebug]
xdebug.remote_enable=On
xdebug.remote_autostart=On
xdebug.remote_connect_back=Off
xdebug.remote_host=10.200.10.1
```

No extra configuration is needed in your IDE (tested on Sublime Text, Visual Studio Code and Atom), apart the usual.

## Extra

### Add some aliases

```bash
alias localhost='cd [projects_dir]/docker-localhost'
alias localhost_up='cd [projects_dir]/docker-localhost && docker-compose up -d'
alias localhost_start='cd [projects_dir]/docker-localhost && docker-compose start'
alias localhost_stop='cd [projects_dir]/docker-localhost && docker-compose stop'
alias localhost_restart='cd [projects_dir]/docker-localhost && docker-compose restart'
```

## Inspiration

[Traefik v2 HTTPS (SSL) on localhost](https://github.com/Heziode/traefik-v2-https-ssl-localhost)
