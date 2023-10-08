# Environment Variables

We use environment variables to provide the unique secret keys and tokens that our webApp needs in order to operate, both in frontend and backend.

Based on the environment that you use for deploying, You will specify these values.

::: tip
If you use Docker or other fancy tools for deploying in a containerized environment, You have to specify these variables at your deployer service, for example [Render](https://render.com) for backend and [Cloudflare Pages](https://pages.cloudflare.com/) for frontend.
:::

::: tip
If you deploy manually and oldSchool way, you can copy the `backend/.env.example` to `backend/.env` and set the values accordingly.
:::

## BackEnd

### Pusher

```shell
PUSHER_APP_ID= app_id of your pusher channel app
PUSHER_KEY= key of your pusher channel app
PUSHER_SECRET= secret of your pusher channel app
PUSHER_CLUSTTER= cluster of your pusher channel app
```

### OpenSwoole
`OPENSWOOLE_SERVER_HOST` Specifies the network interface to bind the OpenSwoole HTTP Server.  

`OPENSWOOLE_SERVER_PORT` Specifies the port number that OpenSwoole HTTP Server listens on.

```shell
OPENSWOOLE_SERVER_HOST=0.0.0.0
OPENSWOOLE_SERVER_PORT=9501
```

### Etc
`WEBAPP_AUTH_TTL` Specifies the TTL that the provided `auth_date` in `initDataUnsafe` by Telegram client is valid (ofcourse after the HMAC validation) in seconds.  

`BOT_TOKEN` Specifies the Token of the Bot that WebApp is being launched from, it's used for HMAC key generation for validation.

```shell
WEBAPP_AUTH_TTL=3600
BOT_TOKEN=123456:XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
```

## FrontEnd

### Pusher
```shell
VITE_PUSHER_APP_ID= app_id of your pusher channel app
VITE_PUSHER_KEY= key of your pusher channel app
VITE_PUSHER_SECRET= secret of your pusher channel app
VITE_PUSHER_CLUSTTER= cluster of your pusher channel app
```

### TeleVue
`VITE_HEX_HMAC_SIGNATURE` is an optional value that specifies the HMAC Signature of your bot token, used for client-side validation of the the webApp client by `AuthProvider` of TeleVue. [You can read more about it and how to generate it here](https://erfanmola.github.io/TeleVue/?path=/docs/providers-authprovider--docs#hex_hmac_signature).

If not specified or empty, SimpList ignores the `AuthProvider` of TeleVue.

```shell
VITE_HEX_HMAC_SIGNATURE= hmac signature of your bot token
```

### Etc
`VITE_BACKEND_ENDPOINT` specifies the endpoint that the backend is deployed to, **It must be without trailing slash**. 

```shell
VITE_BACKEND_ENDPOINT= backend endpoint without trailing slash
```