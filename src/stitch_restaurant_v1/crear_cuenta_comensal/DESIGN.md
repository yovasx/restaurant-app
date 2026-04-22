# Design System Strategy: The Digital Sommelier

## 1. Overview & Creative North Star
The objective of this design system is to transcend the "generic app" aesthetic and move toward a **High-End Editorial** experience. We are not just building a discovery platform; we are creating a digital curator that treats food with the same reverence as a printed culinary magazine.

**Creative North Star: "The Sensory Gallery"**
Our design breaks the rigid, "boxy" nature of traditional SaaS by embracing **intentional asymmetry** and **tonal depth**. We treat the screen as a canvas where high-quality photography is the hero, and the UI acts as a sophisticated, quiet frame. By using overlapping elements (e.g., a dish name partially overlaying an image) and high-contrast typography scales, we create a rhythmic flow that guides the user through a curated gastronomic journey.

---

## 2. Colors & Surface Philosophy
The palette is rooted in warmth—mimicking the natural tones of heirloom tomatoes, toasted grains, and linen tablecloths.

*   **Primary (`#9E2016` / `#C0392B`):** Use for high-intent actions and "appetite" accents.
*   **Secondary (`#944A00`):** Used for navigation cues and subtle highlights.
*   **Surface & Background (`#FFF8F2`):** A warm cream base that feels more premium and less sterile than pure white.

### The "No-Line" Rule
**Explicit Instruction:** Do not use 1px solid borders for sectioning content. Modern luxury is defined by seamless transitions. Define boundaries solely through background shifts:
*   Use `surface-container-low` for a page section.
*   Place a `surface-container-lowest` card on top of it.
*   This creates a "color-block" boundary that is felt rather than seen.

### Surface Hierarchy & Nesting
Treat the UI as a series of physical layers. Use the `surface-container` tiers to define importance:
1.  **Base Layer:** `surface` (The canvas).
2.  **Sectioning:** `surface-container-low` (Subtle grouping).
3.  **Interactive Elements:** `surface-container-lowest` (The "white" card that pops against the cream).
4.  **Elevation:** `surface-bright` (For floating headers or active states).

### The Glass & Gradient Rule
To avoid a flat, "out-of-the-box" feel, use **Glassmorphism** for floating elements (like a "Back to Top" button or a floating "Reserve" bar). Use `surface` at 80% opacity with a `backdrop-blur-md` effect. For primary CTAs, apply a subtle linear gradient from `primary` to `primary_container` to give the button "soul" and a tactile, slightly curved appearance.

---

## 3. Typography: Editorial Authority
We pair **Plus Jakarta Sans** for high-impact displays with **Inter** for utilitarian clarity.

*   **Display & Headlines (Plus Jakarta Sans):** These are your "hooks." Use `display-lg` (3.5rem) for hero sections. The tight kerning and geometric shapes of Jakarta Sans convey modern professionalism.
*   **Body & Labels (Inter):** Humanist sans-serif that ensures legibility in Spanish—accommodating longer word lengths (e.g., *"Recomendaciones"* vs *"Picks"*) without breaking the layout.
*   **Hierarchy as Brand:** Use `headline-lg` for restaurant names to give them an authoritative presence. Use `label-sm` in all-caps with `tracking-wider` for categories (e.g., "COCINA MEXICANA") to create an upscale, tagged look.

---

## 4. Elevation & Depth
We eschew traditional "drop shadows" in favor of **Tonal Layering**.

*   **The Layering Principle:** Depth is achieved by stacking. A `surface-container-lowest` card (Pure white) sitting on a `surface-container-low` (Warm cream) section provides a natural lift.
*   **Ambient Shadows:** If a shadow is required (e.g., a modal), use `on_surface` at 6% opacity with a 24px blur. It should feel like a soft glow, not a hard edge.
*   **The "Ghost Border" Fallback:** For accessibility in form fields, use `outline-variant` at 20% opacity. 100% opaque borders are strictly forbidden as they clutter the visual field.

---

## 5. Components & Primitive Styles

### Buttons
*   **Primary:** `bg-primary`, `rounded-md` (8px), text-white. Use the signature gradient.
*   **Secondary:** `bg-secondary-fixed-dim` with `on-secondary-fixed` text. No border.
*   **Tertiary:** Ghost style. No background, `text-primary`, underline on hover.

### Cards & Lists (The "No-Divider" Rule)
*   **Forbid the use of divider lines (`<hr>`).** 
*   Separate list items using `py-4` (Spacing 4) or subtle background shifts.
*   **Restaurant Cards:** Use `rounded-lg` (16px) for images. The image should be the dominant force. Place the rating/price tag as a glassmorphic overlay in the top-right corner of the image.

### Input Fields
*   **Default State:** `bg-surface-container-highest` with a 0px border.
*   **Focus State:** `ring-2 ring-primary-fixed` with a subtle `surface-container-lowest` background shift.
*   **Spanish Context:** Ensure helper text (`body-sm`) accounts for gendered language and longer descriptors.

### Floating Action Component (Contextual)
For a food app, we introduce the **"Quick-Cart" / "Direct Reserve"** floating bar. It should use `surface-container-lowest` with a 12px blur backdrop, sitting at the bottom of the mobile viewport with a `xl` (24px) border radius on the top corners only.

---

## 6. Do's and Don'ts

### Do
*   **Use Asymmetry:** Place a restaurant description slightly offset from its hero image to create a custom, "designed" feel.
*   **Embrace White Space:** Use the `20` (5rem) spacing token between major sections. High-end brands aren't afraid of empty space.
*   **Photography First:** Use high-resolution, warm-toned food photography. If the photo is low quality, the system fails.

### Don't
*   **No Hard Outlines:** Never use a `#000000` or high-contrast border. Use tonal shifts.
*   **No Pure Greys:** Ensure all "neutral" tones are tinted with a hint of warm orange/red to maintain the "Food-Forward" warmth.
*   **No Default Hover Effects:** Avoid simple opacity changes. Use subtle scale-ups (`scale-102`) and smooth transitions (`duration-300`) for a premium feel.

---

## 7. Implementation (Tailwind / Vue Structure)
The system should be implemented using functional CSS classes. 