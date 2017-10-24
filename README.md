# [Dashi](https://github.com/DannyFeliz/Dashi)
Get a notification in Slack every time someone asks you to check some code on Github or Bitbucket.


# Installation 

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
Run php artisan key:generate

# generate optimized class loader
php artisan optimize

# run the migrations
php artisan migrate

# run the seeding methods
php artisan db:seed

```

## Usage

In order to start using Dashi in your daily routine, you need to: 

1. Generate a Slack Incoming WebHook and copy the URL.
2. Signup at [Dashi](http://dashinotify.com/register)
3. Type your Github or Bitbucket username
4. Paste the copied Slack Webook URL

You can visit the [Dashi homepage](http://dashinotify.com) to get a detailed guide.

## Contributing

See the [CONTRIBUTING](CONTRIBUTING.md) file.

## License

[MIT](http://opensource.org/licenses/MIT)
