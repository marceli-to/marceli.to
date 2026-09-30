# TODO

- [ ] **Serve images as AVIF.** `resources/views/partials/media/image.antlers.html` offers WebP and JPEG sources per breakpoint. Add an AVIF `<source>` before the WebP one for each size, with matching `*-avif` Glide presets in `config/statamic/assets.php`. Check that the production PHP image driver (GD or Imagick) can encode AVIF.
- [ ] **Never publish a PNG; convert to JPG first.** The MCP screenshot upload (`app/Mcp/Support/Screenshots.php`) currently keeps PNGs as `.png`. Always encode to JPEG instead, and make sure `publish_project` never publishes a project whose screenshot is still a PNG.
