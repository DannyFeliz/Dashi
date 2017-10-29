# [Dashi](https://github.com/DannyFeliz/Dashi)
Get a notification in Slack every time someone asks you to check some code on Github or Bitbucket.

## Usage

In order to start using Dashi in your daily routine, you need to: 

1. Generate a [Slack Incoming WebHooks](https://slack.com/apps/A0F7XDUAZ-incoming-webhooks) and copy the URL.
2. Signup at [Dashi](http://dashinotify.com/register)
3. Type your Github or Bitbucket username
4. Paste the copied Slack Webook URL

You can visit the [Dashi homepage](http://dashinotify.com) to get a detailed guide.

## Development setup

Make sure you have installed PHP and MySQL/MariaDB.

Create the database:

```sql
CREATE DATABASE dashi
  DEFAULT CHARACTER SET utf8
  DEFAULT COLLATE utf8_general_ci;
```
Install [Composer](https://getcomposer.org/download/).

Now, in your terminal: 

```bash
# cd /home or wherever you want to install it
cd /home

# clone the project
git clone https://github.com/DannyFeliz/Dashi.git
cd Dashi

# Copy the .env.example file as .env and edit with your local settings
cp .env.example .env

# run composer
composer update

# generate the app key
php artisan key:generate

# generate optimized class loader
php artisan optimize

# run the migrations
php artisan migrate

# run the seeding methods
php artisan db:seed

# serves the app
php artisan serve
```

## Contributing

Read our [contributing guide](https://github.com/DannyFeliz/Dashi/blob/repository-docs/.github/CONTRIBUTING.md) to learn about our development process, how to propose bugfixes and improvements, and how to create a pull request.

## License

[MIT](LICENSE.txt)
