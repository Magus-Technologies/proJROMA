# Taste (Continuously Learned by [CommandCode][cmd])

[cmd]: https://commandcode.ai/

# api-routing
- When adding features to existing functionality, modify existing routes/views instead of creating separate parallel systems under a new prefix or route. Confidence: 0.70

# browser-automation
- Use agent-browser (npx agent-browser) instead of chrome-devtools-mcp for browser automation and login testing. Confidence: 0.65

# code-conservation
- Prefer using existing code, configurations, and structures instead of modifying or replacing them when they already serve the purpose. Confidence: 0.90

# filament-ui
- Keep Filament input borders thin (override default focus ring with subtle box-shadow border). Confidence: 0.70
- Use modal-based CRUD (create/edit in modals) for simple resources with few fields instead of separate pages. Confidence: 0.70
- Migrate all app modules to Filament Resources as one unified system, eliminating old standalone views afterward. Confidence: 0.70

