# Technologies Used
In this section, we are going to describe every technology used in both frontend and backend of the project. We are going to explain the usage of each tool in the SimpList.

## FrontEnd
Our aim in the frontend side of the project is to provide a modern development experience while keeping the project understandable and maintainable. That's why we chose the technologies and tools below.

### Vite
We scaffolded our project using the [Vite](https://vitejs.dev/). It's blazingly fast compared to it's competitors, just like comparing Telegram to it's rivals. (What was the name? Wat Sap or something?)

### Sass
We styled our project using the SCSS format of the [Sass](https://sass-lang.com/). It's just a prefernce, but most people do prefer SCSS since it's just regular CSS syntax with superpowers. We know that Tailwind thingy exists, but nah, we don't pollute markup with article-long class names.

### VueJS
VueJS is our **#1** choice of web frameworks. You ask the reason why? Cause the sky is high, also elephants can't fly. How can you just not like the cool [VueJS](https://vuejs.org/) with it's Composition API? But jokes aside, any framework could have been used, all those Reacty and Angulary and Svelty guys are ok to use. Using VueJS is just a personal choice of the developer.

#### `vue-router`
We use [Vue Router](https://router.vuejs.org/) as our routing system in the SPA.

#### `vue-i18n`
We use [Vue i18n](https://vue-i18n.intlify.dev/) as our i18n provider for multi-language setup.

#### `vue3-touch-events`
We use [Vue Touch Events](https://github.com/robinrodricks/vue3-touch-events) for detecting touch actions like swiping or etc.

#### `vue3-lottie`
We use [Vue3 Lottie](https://www.npmjs.com/package/vue3-lottie) as our Lottie renderer in Vue.

### TeleVue
We developed an exclusive Telegram UI Library for VueJS, just for the purpose of this WebApp and Telegram Mini App Contest. If you are both a Telegram Enjoyer and VueJS's fiancé, then you should try [TeleVue](https://github.com/erfanmola/TeleVue) for you next TWA (Telegram Web App), I know they call it MiniApps, but they are wrong, yeah Telegram, you are wrong.

### Lottie
We use [Lottie](https://airbnb.design/lottie/) animations as our fancy-dancy-dynamically-scalably-animated-graphics. Right now it's only used to render that animated thingy in the header of SimpList's Homepage, it also bloats the bundle size by some hundreds of kilobytes, but yeah it's a contest and we must be fancy to win, now one cares about well-documented and well-commented and well explained project. We are gonna win just for that fancy Lottie in our header. 

::: info
The package used for rendering Lottie in Vue is [explained here](#vue3-lottie).
:::

### Pusher
We use [Pusher](https://pusher.com/) as our WebSockets provider for the Real-Time Synchronization of the tasks between multiple telegram clients of the user.

### i18n
We use the i18n standard for defining our locales and making SimpList multilingual.

::: tip
We are welcoming translators to help us translate the SimpList to different languages, you can start from `webApp/src/i18n` directory.
:::

::: info
The package used for i18n in Vue is [explained here](#vue-i18n).
:::

### SparkMD5
We use [SparkMD5](https://www.npmjs.com/package/spark-md5) for calculating the MD5 hash when generating the `taskId`.

### TWA

We use [@twa-dev/sdk](https://github.com/twa-dev/SDK) as a replacement for the legacy `telegram-web-app.js`. Telegram web dudes are a bunch of C++ developers familiar with HTML/CSS, they use JQuery and Bootstrap from the early 80's and they don't provide a modular solution nor a package for their Javascripts, that's why we use this as package a replacement. 

### Eruda

We use [Eruda](https://github.com/liriliri/eruda) console in dev mode as our DevTools alternative in Mobile platforms. I'm an i3wm user who [cannot use webApps on desktop](https://github.com/telegramdesktop/tdesktop/issues/26288), also an iOS user who has [no DevTools in mobile](https://core.telegram.org/bots/webapps#debug-mode-for-mini-apps), that's why I have to use this alternative console. You guys with Android/Windows/MacOS/Linux(supported DE's) will not have this problem.

## BackEnd
Initally our aim was to go completely serverless, but [Pusher](#pusher) forced us to authorize our clients at server-side, that's we made a tiny backend for this purpose, **NO DATA IS STORED IN SERVER**.

### PHP
We use [PHP](https://php.net) (Pretty Hard Programming) language as our backend language. You ask the reason why? Why would I use anything else? Why? You think Javascript or ASP.net or Python or etc is cool? I don't like cool things. But jokes aside, there is really no difference in our case for what language to use.

#### `phpdotenv`
We use [phpdotenv](https://github.com/vlucas/phpdotenv) for parsing our environment variables.

### OpenSwoole
Since we are using PHP, we supercharge our PHP with [OpenSwoole](https://openswoole.com/) HTTP Server and serve our backend with it. We load our backend code and env variables in Memory once and our Server is up and running, listening for requests.

### Pusher
We authorize the user in the backend and generate a [pusher](#pusher) authorization token and hand it to the client.