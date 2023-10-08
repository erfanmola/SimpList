# Pusher

As [explained in the Technologies used](/technologies-used#pusher), we need [Pusher](https://pusher.com/) as our WebSockets provider for the Real-Time Synchronization.

For this matter, we need to create a Pusher account and [login to pusher dashboard](https://dashboard.pusher.com/) to continue.

## Create Pusher App

We need to create a new pusher Channels app in order to continue, so you can do it yourself dude, I'm not gonna record a youtube tutorial for how you can create an app in Pusher, you are a developer! don't act like a baby.

You can choose any `name` and `cluster` you want, but for tech stack, you may need to choose `Vue` for FrontEnd, `PHP` for the BackEnd and `Vacation` for the WeekEnd.

## Getting App Keys

After creating the app, you can navigate to the `App Keys` section and copy the `app_id`, `key`, `secret` and `cluster` values, since we are going to use them in the [Environment Variables](/environment-variables).