/*
---

name: settings

description: Admin component for managing settings

license: MIT-style license.

css:
 - settings

provides: [settings]

...
 */

//let's just add the tab for now
$content.add(
    new Jx.Tab({
        active: true,
        close: true,
        label: 'Manage Settings',
        content: '<p>The settings management page will be here eventually.</p>'
    })
);
