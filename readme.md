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

* [Drupal 9](https://www.drupal.org/download) ([Drupal 8 is end-of-life](https://www.drupal.org/psa-2021-11-30))

* PHP 8

* [Composer](https://getcomposer.org/)

## Drupal dependencies

* Several [```ambientimpact_*``` modules](https://github.com/Ambient-Impact/drupal-modules) must be present.
