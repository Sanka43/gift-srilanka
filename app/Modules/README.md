# Modules Directory

This directory is reserved for **future modular architecture** of the application.

## Purpose

- Keep the main `app` namespace focused on core e-commerce (shop, cart, orders, admin).
- Add new business modules (e.g. Real Estate, Events) without mixing them into the core.
- Each module can have its own controllers, models, and routes.

## Structure (when adding a module)

Create a subdirectory per module, for example:

```
app/Modules/
  README.md          (this file)
  RealEstate/        (example future module)
    Controllers/
    Models/
    ...
```

## Guidelines

- Do not add multi-vendor logic to the core Gifts Sri Lanka codebase.
- New modules should be self-contained and registered via service provider or route files as needed.
- This directory is intentionally minimal until a concrete module is introduced.
