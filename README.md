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

#### 1.0.1
* Setup Models and Controllers
* Add expiration functionality
* Update notification bar meta
* Include CP Button SCSS module

#### 1.0.0
* Initial release
