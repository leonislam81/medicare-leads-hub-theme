# Medicare Leads Hub WordPress Theme

Reusable WordPress theme for local service businesses. Site-specific content is stored in WordPress pages, media, Customizer settings, menus, forms, and the database—not in this repository.

## Safe update workflow

1. Make and test code changes in the local WordPress project.
2. Update the `Version` header in `style.css`.
3. Commit the change and create a matching tag, for example `v1.5.1`.
4. Push the branch and tag to GitHub.
5. GitHub Actions builds a theme-only ZIP release named `medicare-leads-hub-<version>.zip`.
6. Upload that ZIP as a theme update on each live WordPress site.

Theme updates do not replace pages, media, forms, SEO settings, menus, or saved Customizer values. Full-site migration is only needed when intentionally moving the whole website.

## Release commands

```bash
git add .
git commit -m "Describe the change"
git tag v1.5.2
git push origin main --follow-tags
```

The tag version must match the `Version` value in `style.css`. Never change the theme folder name or text domain after a site is configured; WordPress uses them to keep the saved theme settings attached to the theme.
