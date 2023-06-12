This contains the source files for the "*Omnipedia - Attached data*" Drupal
module, which provides the attached data framework for
[Omnipedia](https://omnipedia.app/).

⚠️⚠️⚠️ ***Here be potential spoilers. Proceed at your own risk.*** ⚠️⚠️⚠️

----

# Why open source?

We're dismayed by how much knowledge and technology is kept under lock and key
in the videogame industry, with years of work often never seeing the light of
day when projects are cancelled. We've gotten to where we are by building upon
the work of countless others, and we want to keep that going. We hope that some
part of this codebase is useful or will inspire someone out there.

----

# Description

Attached data is our internal name for the content displayed in pop-ups on
Omnipedia. It quickly became apparent during development that we couldn't
manually embed all the pop-up content because it would be a nightmare to keep up
to date. We needed to be able to define pop-up content once and have a system
automatically attach it where ever it's referenced. The resulting system allows
editors to define each one as their own entities which are then automatically
attached to all wiki pages that reference them.

There are currently two types of attached data:

1. Abbreviations: These define the many abbreviations used across Omnipedia; the target string is the abbreviated form and the content is the fully spelled out form.

2. Wikimedia links: These are the paragraph-length pop-ups found all across Omnipedia; the target string is a topic name, and the content is what is displayed in the pop-up; these are so named because at one point we intended these to actually link off-site to Wikipedia and related sites, but decided against it.

----

# Planned improvements

* [Port date-related attached data functionality to the omnipedia_date module](https://github.com/neurocracy/drupal-omnipedia-attached-data/issues/1)

----

# Requirements

* [Drupal 9.5 or 10](https://www.drupal.org/download) ([Drupal 8 is end-of-life](https://www.drupal.org/psa-2021-11-30))

* PHP 8

* [Composer](https://getcomposer.org/)

## Drupal dependencies

Before attempting to install this, you must add the Composer repositories as
described in the installation instructions for these dependencies:

* Several of the [`ambientimpact_*` modules](https://github.com/Ambient-Impact/drupal-modules).

* The [`omnipedia_content`](https://github.com/neurocracy/drupal-omnipedia-content), [`omnipedia_core`](https://github.com/neurocracy/drupal-omnipedia-core), and [`omnipedia_date`](https://github.com/neurocracy/drupal-omnipedia-date) modules.

----

# Installation

## Composer

### Set up

Ensure that you have your Drupal installation set up with the correct Composer
installer types such as those provided by [the `drupal/recommended-project`
template](https://www.drupal.org/docs/develop/using-composer/starting-a-site-using-drupal-composer-project-templates#s-drupalrecommended-project).
If you're starting from scratch, simply requiring that template and following
[the Drupal.org Composer
documentation](https://www.drupal.org/docs/develop/using-composer/starting-a-site-using-drupal-composer-project-templates)
should get you up and running.

### Repository

In your root `composer.json`, add the following to the `"repositories"` section:

```json
"drupal/omnipedia_attached_data": {
  "type": "vcs",
  "url": "https://github.com/neurocracy/drupal-omnipedia-attached-data.git"
}
```

### Installing

Once you've completed all of the above, run `composer require
"drupal/omnipedia_attached_data:4.x-dev@dev"` in the root of your project to have
Composer install this and its required dependencies for you.

-----------------

# Breaking changes

The following major version bumps indicate breaking changes:

* 4.x:

  * Requires Drupal 9.5 or [Drupal 10](https://www.drupal.org/project/drupal/releases/10.0.0).

  * Increases minimum version of [Hook Event Dispatcher](https://www.drupal.org/project/hook_event_dispatcher) to 3.1 and adds support for 4.0 which supports Drupal 10.

  * Removes the `omnipedia_attached_data_migrate` module; you can still find it in the 3.x branch.
