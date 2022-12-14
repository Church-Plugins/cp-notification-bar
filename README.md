# CP Notification Bar
Easy and customizable notification bars

##### First-time installation  #####

- Copy or clone the code into `wp-content/plugins/cp-notification-bars/`
- Run these commands
```
composer install
npm install
cd app
npm install
npm run build
```

##### Dev updates  #####

- There is currently no watcher that will update the React app in the WordPress context, so changes are executed through `npm run build` which can be run from either the `cp-notification-bars`

### Change Log

#### 1.0.2
* Fix bug that was causing a service check every cron, even when there wasn't a schedule set.

#### 1.0.1
* Update Church Plugins core

#### 1.0.0
* Initial release
