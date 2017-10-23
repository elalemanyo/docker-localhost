# Docker localhost
This project provides a basic Docker setup, for building a local development environment.


##  Requirements
- [Docker](https://www.docker.com)
- Docker Compose, Docker App and Docker Toolbox already include Compose along with other Docker apps, so most users do not need to install Compose separately.

## Features
- [Træfik](https://traefik.io) HTTP reverse proxy and load balancer made to deploy microservices with ease.
- [Portainer](https://portainer.io/) Simple management UI for Docker.
- [MailHog](https://github.com/mailhog/MailHog) Web and API based SMTP testing.

## Installation / run
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
echo "address=/.dev/127.0.0.1" > /usr/local/etc/dnsmasq.conf
```

Install the daemon startup file

```bash
sudo cp -fv /usr/local/opt/dnsmasq/*.plist /Library/LaunchDaemons
```

Start the daemon

```bash
sudo launchctl load /Library/LaunchDaemons/homebrew.mxcl.dnsmasq.plist
```

All we need to do is tell the resolver to use Dnsmasq for .dev domains:

```bash
# man 5 resolver
sudo mkdir -p /etc/resolver
sudo sh -c 'echo "nameserver 127.0.0.1" > /etc/resolver/dev'
```

Now you can now use any .dev domain and it will always resolve to 127.0.0.1.<br/>
You can easily create new domains on the fly, and never have to worry about your /etc/hosts file again.

**Source:** [Setting up a wildcard DNS domain on Mac OS X](http://asciithoughts.com/posts/2014/02/23/setting-up-a-wildcard-dns-domain-on-mac-os-x/) - [ASCII Thoughts](http://asciithoughts.com)

## Accessing services

- [Træfik](http://localhost.dev:8080/)
- [Portainer](http://portainer.dev)
- [MailHog](http://mailhog.dev/)


## Setting a new Project
Add Træfik labels in container from docker-comose.yml:

```
labels:
  - 'traefik.backend=${PROJECT_NAME}-web'
  - 'traefik.port=80'
  - 'traefik.frontend.rule=Host:${PROJECT_NAME}.dev'
```

And add at the end the network:

```
networks:
  default:
    external:
      name: dockerlocalhost_default
```

### Setting MailHog
#### PHP
Add [mhsendmail](https://github.com/mailhog/mhsendmail) a sendmail replacement for MailHog:

```
volumes:
	- docker-localhost/mhsendmail_linux_amd64.dms:/usr/local/bin/mhsendmail
```

 Set php to use it. Add these lines to `php.ini`:

 ```php
 sendmail_path = "/usr/local/bin/mhsendmail --smtp-addr="mailhog:1025""
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

### Extra

#### Add some aliases

```bash
alias localhost='cd /docker-localhost'
alias localhost_up='cd /docker-localhost && docker-compose up -d'
alias localhost_start='cd /docker-localhost && docker-compose start'
alias localhost_stop='cd /docker-localhost && docker-compose stop'
alias localhost_restart='cd /docker-localhost && docker-compose restart'
```
