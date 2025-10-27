# Eternal Slug

When you manually change an entry’s slug or move it within a Structure, Craft CMS updates the URL — but the old one instantly stops working.
As a result, any user or search engine visiting the previous link will land on a **404 Not Found** page.
This not only hurts user experience but can also cause **SEO value loss** and **broken backlinks**.

**Eternal Slug** fixes this automatically.

Whenever an entry’s slug changes or its position in a Structure is updated, Eternal Slug instantly creates a **301 redirect** from the old URL to the new one and no manual action is required.

Your visitors and search engine crawlers (like Googlebot) are seamlessly guided to the correct page, preserving both **traffic** and **ranking signals**.

**Keep your URLs alive with Eternal Slug**

## Requirements

This plugin requires:
- **Craft CMS** 5.8.0 or later
- **PHP** 8.2 or later

## Installation

You can install this plugin from the **Plugin Store** or with **Composer**.

#### From the Plugin Store

1. Open the **Plugin Store** in your project’s Control Panel.
2. Search for **“Eternal Slug”**.
3. Click **Install** to complete the installation.

#### With Composer

Open your terminal and run the following commands:

```bash
# go to the project directory
cd /path/to/my-project

# tell Composer to load the plugin
composer require paxxion/craft-eternal-slug

# tell Craft to install the plugin
./craft plugin/install eternal-slug
```

## Usage

Once installed, **no additional configuration is required**.  
Eternal Slug will automatically generate redirects whenever:
- an entry’s slug is changed
- an entry is moved within a Structure

## Settings

#### HTTP status code

You can set the HTTP status code to be used for redirects — **301 (Permanent)** or **302 (Temporary)**.  
This setting is **global** and applies **retroactively** to existing redirects.

#### Export redirects

You can download an **Excel (.xlsx)** file containing the list of all currently active redirects.

#### Url History UI Element

The **Url History UI element** can be added to the **Field Layout** of a **Entry Type**.  
It provides a record of all URL changes associated with an entry.

## Support

For questions, issues, or feature requests, please visit the [project repository on GitHub](https://github.com/paxxion/craft-eternal-slug) and open a new **issue**.