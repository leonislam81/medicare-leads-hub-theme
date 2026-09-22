# Local Service Starter: Reuse Guide

This theme is split into two layers:

- **Theme shell:** layout, responsive CSS, components, Customizer controls, and reusable section templates.
- **Site profile:** business name, service type, service area, phone, email, logo, colors, images, page copy, menus, homepage selection, forms, testimonials, and SEO settings.

The current Locksmith site keeps its saved Customizer values. A fresh installation receives neutral starter content and does not auto-import the current site's phone number, images, review widget, map, or menu names.

## New domain checklist

1. Install and activate the theme.
2. Set **Settings → General**: Site Title, Tagline, WordPress URL, Site URL, and admin email.
3. Create or import the pages, then set **Settings → Reading** to the correct homepage.
4. Set **Appearance → Customize → Site-wide Settings → Business Profile**.
5. Upload/select the new logo and section images from **Media**. The **Theme preview image** control in Business Profile automatically writes the selected image to the active theme's `screenshot.png` when the Customizer is saved.
6. Update **Global Design System**, page copy, services, menus, contact form, map, and testimonial widget.
7. Configure the SEO plugin for that domain: site title, meta description, canonical URL, Open Graph image, local-business details, and schema.
8. Configure a real recipient inbox in the Contact Us Customizer settings and configure an SMTP/provider plugin on the live domain; localhost can validate the form route but cannot prove external mail delivery.
9. Test the homepage, inner pages, phone links, form delivery, menu links, mobile action bar, sitemap, and robots.txt before launch.

## Migration rule

Copying only the theme gives a clean reusable starter. Copying the entire WordPress database also copies the previous site's pages, media, Customizer values, menus, forms, and SEO settings; those must be intentionally replaced for the new domain.

The internal text-domain/folder slug remains `medicare-leads-hub` for backward compatibility with existing Customizer data. It is not the public business identity and does not determine search rankings.

The theme preview is a package/admin image, not an SEO image. The active theme folder must be writable for the automatic `screenshot.png` update to succeed; otherwise replace that file manually before packaging the theme.
