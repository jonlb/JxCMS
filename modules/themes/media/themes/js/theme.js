/*
---

name: theme

description: theme module admin interface file

license: MIT-style license.

css:
 - theme

provides: [theme]

...
 */

//let's just add the tab for now
$content.add(
    new Jx.Tab({
        active: true,
        close: true,
        label: 'Manage Themes',
        content: '<p>The Theme page will be here eventually.</p>'
    })
);
