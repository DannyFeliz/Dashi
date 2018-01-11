# Dashi Contributing Guide

Hi! We're really excited that you are interested in contributing to Dashi. Before submitting your contribution though, please make sure to take a moment and read through the following guidelines.

- [Code of Conduct](https://github.com/DannyFeliz/Dashi/blob/repository-docs/.github/CODE_OF_CONDUCT.md)
- [Pull Request Guidelines](#pull-request-guidelines)
- [Development Setup](#development-setup)

## Pull Request Guidelines

- The `master` branch is basically just a snapshot of the latest stable release. All development should be done in dedicated branches. **Do not submit PRs against the `master` branch.**

- Checkout a topic branch from the relevant branch, e.g. `dev`, and merge back against that branch.

- It's OK to have multiple small commits as you work on the PR - we will let GitHub automatically squash it before merging.

- If adding new feature:
  - Provide convincing reason to add this feature. Ideally you should open a suggestion issue first and have it greenlighted before working on it.

- If fixing a bug:
  - If you are resolving a special issue, add `(fix #xxxx[,#xxx])` (#xxxx is the issue id) in your PR title for a better release log, e.g. `update entities encoding/decoding (fix #3899)`.
  - Provide detailed description of the bug in the PR.

- Use the [Pull Request Template](https://github.com/DannyFeliz/Dashi/blob/repository-docs/.github/PULL_REQUEST_TEMPLATE.md) 

## Development Setup

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

```
