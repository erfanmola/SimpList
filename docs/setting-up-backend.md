# Setting Up the BackEnd

After reading the [Technologies used in backend](/technologies-used#backend) and [Setting up the FrontEnd](/setting-up-frontend), We are ready to continue the setup, build and deployment process of the SimpList's BackEnd.

We obviously need `PHP` to be installed, after that we have two options when it comes to backend, `manual` and `docker` deployment.

## Docker Method (Recommended)



:::warning
This step requires correct setup of [Environment Variables](/environment-variables), otherwise you will face errors.
:::

### Method 1: Deploy On [Render.com](https://render.com) (Recommended)
Our recommended method of deploying backend using docker, is to deploy it at [Render.com](https://render.com), You need to login to your account and create a new `Web Service` project, then you will connect your Github repository containing the backend or you can use the original public repository of SimpList.

Afterwards, you will have to click on `Advanced` and set the `Docker Build Context Directory` to `backend` directory, and you must set all the [Environment Variables](/environment-variables#backend) needed for backend. Then you can click on `Create Web Service` and enjoy the deployment as it proceeds.

### Method 2: Deploy On Other Infrastructures

Just like previous method, you can follow the same process on any containerized infrastructure.

### Method 3: Build Image from the Dockerfile
```shell
cd backend
$ sudo docker buildx build . -t erfanmola/simplist
$ sudo docker run -it erfanmola/simplist
```

### Method 4: Pull Image from DockerHub

```shell
$ sudo docker pull erfanmola/simplist
$ sudo docker run -it erfanmola/simplist
```

## Manual Method

In this method, we have to change our Working Directory to `backend` folder, since it's the folder containing our backend code:
```shell
cd backend
```

### Installing OpenSwoole
> Open Swoole is a high-performance network framework based on an event-driven, asynchronous, non-blocking I/O coroutine programming model for PHP.

OpenSwoole is a PHP Extension, which means you will need a bit more effort to install it manually than a library needs. This is a [dedicated guide of installing OpenSwoole](https://openswoole.com/docs/get-started/installation), but we are going to summarize it.

#### Method 1: Install using PECL

In this method, you need to satisfy the build dependencies of OpenSwoole ourselves, then we can retreive and build it using PECL:

```shell
$ sudo pecl install openswoole
```

#### Method 2: Install using OS Package Manager (Ubuntu for example)

```shell
apt update
apt install -y software-properties-common && add-apt-repository ppa:ondrej/php -y
apt install -y software-properties-common && add-apt-repository ppa:openswoole/ppa -y
apt install -y php8.2-openswoole
```

#### Method 3: Install using Docker

```shell
docker pull openswoole/swoole
```

:::info
By installing OpenSwoole using Docker, it's not natively installed on your system, so you will need some Docker basic knowledge to use it. [You can read more here](https://github.com/openswoole/docker-openswoole)
:::

### Installing Dependencies

At this step, we need to install our project's dependencies. We use [Composer](https://getcomposer.org/) as our package manager.

```shell
composer install
```

### Serving the project

You can serve the project by running the `server.php` file:

```shell
php server.php
```

:::warning
This step requires correct setup of [Environment Variables](/environment-variables), otherwise you will face errors.
:::

:::info
The server provided by OpenSwoole HTTP Server keeps running in the background, unlike the usual expectation from a `php file.php` command. You can start it using a process manager like `Supervisor` or fork it and pipe it's `stdout` and `stderr` to `/dev/null`.
:::