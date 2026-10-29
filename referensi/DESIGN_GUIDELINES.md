# DESIGN_GUIDELINES.md
## Design System & UI/UX Implementation Guide

> **Purpose:** This document defines the visual language, UI/UX principles, reusable components, responsive behavior, and implementation standards for this website.
>
> **Visual reference:** The uploaded Finora dashboard screenshot.
>
> **Core direction:** Modern SaaS dashboard — clean, calm, data-focused, light, friendly, and professional.
>
> **Important:** Use the screenshot as a visual reference, not as a requirement to copy its exact content or page structure. Adapt the system to the actual product, users, data, and workflows.

---

## 1. Design Goals

Every page should feel:

- **Clear:** users can quickly understand the page and identify the most important information.
- **Consistent:** colors, spacing, typography, controls, cards, and interaction patterns follow the same system.
- **Professional:** polished, reliable, and appropriate for a production application.
- **Lightweight:** avoid unnecessary decoration, excessive gradients, heavy shadows, and visual clutter.
- **Data-friendly:** present metrics, charts, tables, statuses, and actions in an easy-to-scan hierarchy.
- **Responsive:** usable on desktop, tablet, and mobile.
- **Accessible:** readable contrast, keyboard-friendly controls, clear focus states, and meaningful labels.
- **Maintainable:** build reusable components and design tokens rather than duplicating styling across pages.

### Priority order

When trade-offs are necessary, prioritize:

1. Correct functionality and data integrity.
2. Usability and accessibility.
3. Clear information hierarchy.
4. Consistency with this design system.
5. Decorative polish.

Do not sacrifice usability or readability merely to match a screenshot.

---

## 2. Visual Identity

### Overall aesthetic

Use a light, modern dashboard aesthetic with:

- A very pale blue-gray page background.
- White or near-white content surfaces.
- Dark navy headings and primary text.
- Muted blue-gray secondary text.
- A restrained blue primary accent.
- Soft pastel colors for categories and statuses.
- Rounded corners and subtle borders.
- Soft, low-contrast shadows used sparingly.
- Generous whitespace and aligned content edges.

Avoid a dark theme unless the product specifically requires it. Do not add glassmorphism, neon colors, strong gradients, excessive animation, or ornamental elements by default.

---

## 3. Design Tokens

Define tokens centrally using CSS custom properties or the project's existing token system. These values are initial recommendations; refine them to fit the product while preserving the overall visual direction.

```css
:root {
  /* Surfaces */
  --color-page: #F3F7FC;
  --color-surface: #FFFFFF;
  --color-surface-subtle: #F8FAFD;
  --color-surface-selected: #E8F2FF;

  /* Text */
  --color-text-primary: #14264A;
  --color-text-secondary: #64748B;
  --color-text-muted: #8A9AB0;
  --color-text-inverse: #FFFFFF;

  /* Brand and interaction */
  --color-primary: #3988F5;
  --color-primary-hover: #2675E5;
  --color-primary-soft: #E7F1FF;
  --color-border-focus: #78AEFF;

  /* Borders and shadows */
  --color-border: #E6EDF5;
  --color-border-strong: #D7E2EF;
  --shadow-card: 0 3px 12px rgba(35, 72, 120, 0.06);
  --shadow-popover: 0 8px 28px rgba(35, 72, 120, 0.12);

  /* Semantic colors */
  --color-success: #1DAA79;
  --color-success-soft: #E4F8EF;
  --color-warning: #D99A19;
  --color-warning-soft: #FFF4D8;
  --color-danger: #E64F67;
  --color-danger-soft: #FFE9ED;
  --color-info: #3988F5;
  --color-info-soft: #E7F1FF;

  /* Category accents — use consistently */
  --color-category-blue: #4E91F7;
  --color-category-green: #35C49A;
  --color-category-yellow: #F2C64B;
  --color-category-red: #EF7180;
  --color-category-purple: #A985F5;

  /* Typography */
  --font-sans: Inter, ui-sans-serif, system-ui, -apple-system,
               BlinkMacSystemFont, "Segoe UI", sans-serif;

  /* Spacing */
  --space-1: 4px;
  --space-2: 8px;
  --space-3: 12px;
  --space-4: 16px;
  --space-5: 20px;
  --space-6: 24px;
  --space-8: 32px;
  --space-10: 40px;
  --space-12: 48px;

  /* Radius */
  --radius-sm: 8px;
  --radius-md: 12px;
  --radius-lg: 16px;
  --radius-xl: 20px;
  --radius-pill: 999px;

  /* Layout */
  --sidebar-width: 232px;
  --content-max-width: 1600px;

  /* Motion */
  --duration-fast: 140ms;
  --duration-normal: 220ms;
}
```

### Token rules

- Use tokens rather than scattering arbitrary hex values, spacing, radii, and shadows throughout the code.
- Use the primary blue for the main action and selected navigation state—not for every element.
- Reserve semantic colors for meaningful states or data categories.
- Ensure text and interactive elements have sufficient contrast.
- Do not communicate status by color alone; include text, an icon, or another visual cue.

---

## 4. Typography

Use **Inter** if available; otherwise use a clean system sans-serif stack. Avoid introducing several font families.

Suggested hierarchy:

| Role | Suggested size | Weight | Guidance |
|---|---:|---:|---|
| Page title | 24–30 px | 650–750 | One clear title per page |
| Section heading | 16–20 px | 600–700 | Use for major content sections |
| Card heading | 14–16 px | 600 | Short and scannable |
| Body text | 14–16 px | 400–500 | Comfortable reading |
| Secondary text | 12–14 px | 400 | Labels, descriptions, metadata |
| Table text | 12–14 px | 400–500 | Keep readable; do not shrink excessively |
| KPI value | 24–32 px | 650–750 | Emphasize the metric |
| Small badge | 11–12 px | 500–600 | Use sparingly |

Rules:

- Use consistent line-height and heading hierarchy.
- Avoid all-caps text for ordinary labels.
- Do not use tiny text simply to fit more information.
- Use tabular numerals for financial figures or aligned numeric columns when supported.
- Display dates, currencies, and numbers in the user's locale.

---

## 5. Layout and Grid

### Desktop layout

Use a persistent left sidebar and a flexible main content area when the product benefits from application-style navigation.

- Sidebar: approximately 220–240 px on large screens.
- Main content: flexible width with consistent outer padding.
- Use a 12-column grid or CSS Grid for dashboard compositions.
- Keep card edges and section headings aligned.
- Use consistent gaps, typically 16–24 px between major components.
- Use a sensible maximum content width on very large screens.
- Avoid horizontal scrolling at common desktop sizes unless the data table genuinely requires it.

Suggested structure:

```text
Application shell
├── Sidebar
│   ├── Brand / logo
│   ├── Primary navigation
│   └── Account or utility area
└── Main area
    ├── Top bar (search, notifications, profile)
    ├── Page heading / contextual information
    ├── Primary metrics or key actions
    ├── Main content grid
    └── Supporting content / footer where needed
```

This is a reusable pattern, not a mandatory structure for every page. A form, detail page, checkout, or onboarding flow may need a different layout.

### Spacing principles

- Use the spacing tokens consistently.
- Give cards enough internal padding, normally 16–24 px.
- Avoid cramped labels and controls.
- Group related information more closely than unrelated information.
- Use whitespace to establish hierarchy rather than adding decorative lines everywhere.

---

## 6. Sidebar and Navigation

The sidebar should be calm, compact, and easy to scan.

### Requirements

- Display the brand/logo clearly at the top.
- Use one consistent icon style and size.
- Use short, recognizable navigation labels.
- Highlight the current route using a pale blue surface and a primary-blue icon/text treatment.
- Provide hover, keyboard-focus, and active states.
- Keep spacing and alignment consistent.
- Separate destructive or administrative actions from everyday navigation where appropriate.
- Include accessible names for icon-only controls.
- Support a collapsed or drawer-style sidebar on smaller screens.

Do not make every navigation item look like a primary button. The active state should be clear without overpowering the page.

---

## 7. Top Bar and Page Header

Where applicable, the top bar may contain:

- Search.
- Notifications.
- User avatar and account menu.
- Contextual actions.

The page header should contain:

- A clear page title.
- An optional short description.
- Optional breadcrumbs when navigating deeply.
- A date range, filter, or primary action only when relevant.

Rules:

- Do not crowd the header with too many controls.
- Give search inputs a clear purpose and useful placeholder.
- Make notification and account controls keyboard accessible.
- Use real, localized dates and actual application data; do not hard-code the screenshot's sample date.

---

## 8. Cards and Panels

Cards are the main reusable surface in this visual system.

### Default card style

- White background.
- 12–16 px corner radius.
- Subtle border and/or soft shadow.
- 16–24 px padding.
- Consistent heading and content spacing.
- No heavy decorative gradient by default.

Use a card only when it helps group related information. Avoid wrapping every individual line or small element in a separate card.

### KPI / summary cards

A KPI card may contain:

- A concise label.
- A prominent value.
- A contextual icon.
- An optional trend or comparison.
- An optional period label.

Rules:

- Make the primary value visually dominant.
- Label trends clearly, e.g. “+12% vs. last month”.
- Distinguish percentage change from absolute change.
- Do not use green automatically for all positive numbers if the metric's meaning differs.
- Use real data and meaningful empty states; never invent metrics for a production interface.

---

## 9. Buttons, Inputs, and Forms

### Buttons

Define consistent variants:

- **Primary:** main action; solid brand blue with readable text.
- **Secondary:** neutral surface with a border.
- **Tertiary/Ghost:** low-emphasis actions such as “Cancel” or toolbar controls.
- **Danger:** destructive actions, with clear wording and confirmation when appropriate.

Every button must have:

- Default, hover, active, focus-visible, and disabled states.
- A clear action label.
- A reasonable click/tap target (preferably at least 40–44 px high for primary controls).
- A loading state when an operation takes time.

Avoid using a primary button for every action on the same screen.

### Inputs and forms

- Use visible labels; placeholders are not a replacement for labels.
- Provide clear validation and error messages near the relevant field.
- Preserve user input when validation fails.
- Explain required fields.
- Use appropriate input types and autocomplete attributes.
- Ensure keyboard navigation and focus visibility.
- Show success feedback after a completed action.
- Avoid clearing a form silently after an error.

---

## 10. Tables and Data Lists

Use tables when users need to compare records across consistent fields.

### Table guidelines

- Provide a clear header row.
- Align text columns left and numeric columns consistently.
- Use consistent row heights and padding.
- Use subtle separators rather than heavy grid lines.
- Make long content truncate gracefully, with access to the full value where necessary.
- Use badges for meaningful status values.
- Provide loading, empty, and error states.
- Include pagination or another scalable navigation pattern for large datasets.
- Provide search, filtering, sorting, or bulk actions when they are useful for the workflow.
- Make tables responsive; on narrow screens, consider a card/list transformation or a deliberately scrollable table with clear affordance.

Never display fake completion statuses, invented transactions, or misleading values as if they came from a real backend.

---

## 11. Charts and Data Visualization

Use charts only when they improve understanding.

- Line charts: trends over time.
- Bar charts: comparison between categories or periods.
- Donut/pie charts: simple part-to-whole relationships with a small number of categories.
- Progress bars: completion against a defined target.

### Chart quality rules

- Include a meaningful title and, where necessary, a description.
- Label units, axes, time ranges, and categories.
- Use a consistent color mapping across the application.
- Avoid 3D charts and unnecessary visual effects.
- Do not rely on color alone to distinguish series.
- Include tooltips where they add value.
- Ensure chart labels remain legible on smaller screens.
- Provide a textual summary or accessible data alternative when practical.
- Handle missing or zero data explicitly.
- Use actual application data; clearly label sample/demo data.

A donut chart with too many segments is hard to interpret. Prefer a bar chart or table when category comparisons are the primary task.

---

## 12. Status Badges and Semantic Color

Use soft background colors with sufficiently dark text.

Suggested mapping:

- Success/completed/active: green.
- Warning/pending/needs attention: amber.
- Error/failed/overdue: red.
- Informational/selected: blue.
- Neutral/inactive: gray.
- Optional categories: purple or other restrained accents.

Rules:

- Keep each status color consistent across pages.
- Include readable status text.
- Do not use green and red as the only distinction.
- Confirm that the status meaning matches the actual business logic.
- Avoid assigning meaning to colors arbitrarily.

---

## 13. Icons and Illustrations

- Use one icon library consistently, such as Lucide, if compatible with the existing stack.
- Keep most interface icons around 16–20 px, with larger icons only for prominent summary elements.
- Use icons to support labels, not replace essential labels.
- Use consistent stroke weight and alignment.
- Avoid mixing emoji, filled icons, outline icons, and unrelated illustration styles in the same interface.
- Decorative illustrations should be optional and never obstruct primary content.

Do not add icons to every piece of text. Restraint makes the interface look more professional.

---

## 14. Interaction and Motion

Interactions should be subtle, predictable, and useful.

- Use short transitions, typically around 140–220 ms.
- Apply hover states to interactive elements only.
- Use visible focus states for keyboard users.
- Provide feedback for save, delete, submit, upload, and navigation actions.
- Use skeletons or progress indicators for loading states where appropriate.
- Support reduced-motion preferences.
- Avoid parallax, bouncing, excessive scaling, or animation that delays work.
- Never make a control look clickable if it has no action.

For destructive actions, provide confirmation when the impact is significant. For routine actions, avoid unnecessary confirmation dialogs.

---

## 15. Responsive Behavior

The interface must be designed for desktop, tablet, and mobile—not merely scaled down.

### Desktop

- Use the full sidebar and multi-column grid where appropriate.
- Preserve generous spacing and readable chart/table labels.

### Tablet

- Reduce outer padding and grid gaps.
- Allow dashboard cards to reflow into fewer columns.
- Consider a collapsible sidebar.
- Prevent header controls from colliding.

### Mobile

- Replace the persistent sidebar with a drawer or compact navigation pattern.
- Stack cards into one column when needed.
- Keep important actions easy to reach.
- Use touch targets of approximately 44 × 44 px where practical.
- Avoid tiny text and dense multi-column layouts.
- Transform wide tables into mobile-friendly lists/cards or allow controlled horizontal scrolling.
- Ensure dialogs and dropdowns fit within the viewport.
- Prevent horizontal overflow and clipped content.

Test at a minimum of approximately 360 px, 390 px, 768 px, 1024 px, and 1440 px viewport widths. Adapt these checkpoints to the target audience and supported devices.

---

## 16. Accessibility

Aim for WCAG 2.2 AA practices where practical.

- Maintain sufficient text and UI contrast.
- Use semantic HTML elements.
- Keep a logical heading hierarchy.
- Associate form labels and error messages with their inputs.
- Support keyboard navigation.
- Use `:focus-visible` states.
- Add accessible names to icon-only buttons.
- Do not use color as the sole way to communicate meaning.
- Respect reduced-motion preferences.
- Use appropriate alternative text for meaningful images.
- Ensure dialogs manage focus correctly.
- Make charts and complex data available in an alternative format when practical.

Accessibility is part of quality, not a final decorative step.

---

## 17. Empty, Loading, Error, and Success States

Every data-driven feature should define its states before implementation.

- **Loading:** show a skeleton, spinner, or progress indicator appropriate to the task.
- **Empty:** explain that there is no data and, when useful, provide a next action.
- **Error:** explain what failed in plain language and offer retry or recovery when possible.
- **Success:** confirm the action completed.
- **Partial data:** show available information without pretending the dataset is complete.
- **Permission denied:** explain that the user does not have access, without exposing protected data.

Avoid blank panels, broken chart areas, raw stack traces, and silent failures.

---

## 18. Content and Localization

- Use clear, concise language consistent with the product.
- If the product is intended for Indonesian users, use natural Bahasa Indonesia consistently.
- Format Rupiah as `Rp 1.250.000` or according to the application's established locale convention; use one convention consistently.
- Use `Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' })` for currency formatting when appropriate.
- Format dates using a consistent Indonesian locale, for example `id-ID`.
- Use real product terminology and meaningful labels.
- Avoid placeholder Latin text or generic sample content in production pages.
- Do not invent business claims, user records, financial figures, or analytics.

---

## 19. Component Architecture and Code Quality

Translate this design system into reusable components. Adapt the examples to the existing framework rather than introducing a new stack unnecessarily.

Potential component inventory:

```text
AppShell
├── Sidebar / MobileNavigation
├── TopBar
├── PageHeader
├── Button
├── IconButton
├── SearchInput
├── Select / DateRangePicker
├── Card
├── MetricCard
├── StatusBadge
├── DataTable
├── ChartCard
├── ProgressBar
├── Avatar
├── DropdownMenu
├── Modal / Dialog
├── Toast / Notification
├── Skeleton
├── EmptyState
└── ErrorState
```

### Engineering rules

- Inspect the existing repository and stack before making architectural changes.
- Reuse existing components, dependencies, routing, and conventions when reasonable.
- Do not replace working application logic merely to change the visual style.
- Keep business logic separate from presentation where practical.
- Centralize design tokens.
- Avoid duplicated CSS and conflicting global styles.
- Use semantic class names and predictable component APIs.
- Keep components focused and reusable, but do not over-abstract trivial elements.
- Preserve existing API contracts, authentication, authorization, and validation.
- Never expose API keys, secrets, or private environment variables in client code.
- Avoid adding dependencies for functionality already supported by the project.
- Do not create non-functional buttons, fake links, or decorative controls that imply actions.
- Do not hard-code data that should come from the backend.
- Use clear names and remove obsolete code only when safe.
- Do not claim that a feature works unless it has been implemented and tested.

---

## 20. Implementation Workflow for an AI Coding Agent

When asked to create or redesign a page, follow this sequence:

1. **Inspect:** identify the framework, routing, existing components, CSS approach, data sources, and current page behavior.
2. **Understand:** determine the page's user goal, key actions, data hierarchy, and responsive needs.
3. **Plan:** map the page to the design tokens and reusable components in this document.
4. **Implement:** make the smallest coherent set of changes while preserving working functionality.
5. **Connect:** use real APIs and data where available; handle loading, empty, success, and error states.
6. **Validate:** run available linting, type checks, tests, and build commands.
7. **Review visually:** inspect the page at desktop and mobile widths; check alignment, overflow, contrast, spacing, and interaction states.
8. **Report:** summarize what changed, which files were affected, what was tested, and any remaining limitations.

If a requirement is ambiguous, choose the option most consistent with the existing product and this design system. Ask a concise clarification only when the ambiguity materially affects architecture, data integrity, security, or user workflow.

Do not rewrite the whole application when the request only requires a focused page or component change.

---

## 21. Visual QA Checklist

Before considering a page complete, verify:

### Visual consistency
- [ ] Page background, surfaces, text, and accents use the design tokens.
- [ ] Typography hierarchy is clear.
- [ ] Cards, buttons, inputs, badges, and icons are consistent.
- [ ] Spacing and alignment are consistent.
- [ ] Shadows and decorative effects are restrained.

### Usability
- [ ] The page's primary purpose is immediately clear.
- [ ] Primary actions are easy to find.
- [ ] Interactive controls have meaningful behavior.
- [ ] Labels and status text are understandable.
- [ ] Loading, empty, error, and success states are handled.

### Responsive behavior
- [ ] No unintended horizontal overflow.
- [ ] Sidebar/navigation adapts to narrow screens.
- [ ] Cards and grids reflow correctly.
- [ ] Tables and charts remain usable.
- [ ] Dialogs, dropdowns, and menus fit in the viewport.

### Accessibility
- [ ] Keyboard navigation works.
- [ ] Focus states are visible.
- [ ] Contrast is adequate.
- [ ] Form fields have labels.
- [ ] Status is not conveyed by color alone.

### Engineering
- [ ] Existing functionality and routes still work.
- [ ] Data comes from the correct source.
- [ ] No secrets or sensitive data are exposed.
- [ ] Lint/build/tests pass where available.
- [ ] Any known failures or untested areas are reported honestly.

---

## 22. Do / Don't

### Do

- Use a pale blue-gray background with clean white surfaces.
- Use navy for important text and blue for primary actions.
- Maintain consistent spacing, radii, typography, and iconography.
- Use soft semantic colors for statuses.
- Make data easy to scan and compare.
- Build responsive, accessible reusable components.
- Adapt layouts to the purpose of each page.
- Preserve working functionality and real data behavior.

### Don't

- Copy the finance dashboard's content into an unrelated product.
- Force every page into the same card grid.
- Use excessive gradients, shadows, borders, or animations.
- Mix multiple unrelated icon libraries.
- use tiny text to squeeze content into a card.
- Use color as the only status indicator.
- Invent metrics or fake backend functionality.
- Break routing, authentication, APIs, or existing workflows for visual changes.
- Refactor unrelated code without a clear reason.
- Treat the screenshot as a pixel-perfect specification for every viewport.

---

## 23. Final Instruction to the AI

Use this document as the default visual and implementation standard for the website. Before creating a new UI, inspect the existing product and adapt these guidelines to its purpose.

**Target result:** a polished, modern, light SaaS interface inspired by the uploaded Finora reference—consistent, readable, responsive, accessible, data-aware, and maintainable.

When visual fidelity conflicts with real functionality, accessibility, or responsive usability, preserve functionality, accessibility, and usability first.
