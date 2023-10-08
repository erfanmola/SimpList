# Suggestions

I have been around for a while, developing Telegram Web Apps and here are some suggestions that I could make to Telegram Dev Team for improving the overall experience of Telegram WebApps Development for developers.

## Server Side

### Telegram Pages

It would be nice to have a native Telegram Pages system, just as Github Pages or Cloudflare Pages exist, It could make serving WebApps easier and without the need of external static file servers.

Ofcourse a proper caching system could be integrated in the ecosystem based on file changes, so that we could leverage the benefit of 0 loading time experience of webApps.

### CloudSpace

It would be nice to have a `CloudSpace` dedicated for each WebApp per each user, a small space like 50MB per user would be a nice option, just as `CloudStorage` exists, the `CloudSpace` could complement it.

Users can create/edit files and interact with them, for example a drawing webApp that let's users save their works into `CloudSpace` and access them later.

### Real-Time Event System

It would be nice to have a native `WebSocket` connection to Telegram for broadcasting events across all the active sessions of the user in WebApp.

Just like SimpList uses `Pusher` to achieve this Synchronization, Suppose that I have the WebApp open both in my Desktop and iOS Clients at the same time, I add one task from iOS, How could my Desktop client receive the event to update itself? There is no native way of Syncing clients in Real-Time, We have to rely on 3rd party WebSocket implementations.

### CloudStorage improvements

`CloudStorage` was a nice addition to the WebApps, but it could be even better. It's design structure seems very familiar to `LocalStorage` of Javascript, but it could burrow some ideas from other Storages too.

It would be very nice to `setItem` with expiry date, for example a status emoji for the user that gets cleared at max 24 hours.

It would also be very nice to have access to `created_at`, `updated_at`, `accessed_at` meta data for each item, right now I have to store these data in my value field.

It would also be nice to be able to query items by `key` or `value` or `created_at`, `updated_at`, `accessed_at`, for example: Query the tasks that added since last week unix timestamp.

## Client Side

### Installable WebApps (Offline)

WebApps are usually built with modern technology frameworks and they usually have Service Workers and etc to be able to function offline, but Telegram does not provide a solution to leverage this ability.

For example, a ToDo List app can function offline too, it will save it's data to LocalStorage and Syncs to CloudStorage when network is available.

### Provide native client font

WebApps are usually intended to look exactly as Telegram looks, that's why most of the time we load both `Roboto` family and `SF Pro` family and choose what to display based on the client OS, these fonts are near 2MB download size and they block rendering the text until loaded, or the text gets rendered in ugly font and then font re-assign happens which is not a good UX.

It would be nice if Telegram Clients inject their own Native font files into the WebView and make them accessible via CSS, like:

```CSS
font-family: TGFont, Arial, Tahoma;
```