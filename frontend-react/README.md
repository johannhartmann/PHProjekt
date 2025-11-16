# PHProjekt React Frontend

Modern React + TypeScript frontend for PHProjekt 6, built with Vite.

## Overview

This is a new React-based SPA that will gradually replace the legacy Dojo frontend using the **strangler pattern**. Both frontends will coexist during the migration:

- **Legacy Dojo**: Accessible at `/` (existing application)
- **New React**: Accessible at `/react/` (new application)

## Tech Stack

- **React 19** - UI library
- **TypeScript** - Type safety
- **Vite** - Build tool and dev server
- **React Router** - Client-side routing
- **ESLint** - Code linting

## Getting Started

### Installation

```bash
cd frontend-react
npm install
```

### Development

```bash
npm run dev
```

Available at: **http://localhost:3000/react/**

### Build

```bash
npm run build
```

Output: `../public/react/`

### Commands

```bash
npm run dev         # Start dev server (port 3000)
npm run build       # Build for production
npm run lint        # Run ESLint
npm run lint:fix    # Fix ESLint issues
npm run type-check  # TypeScript type checking
npm run preview     # Preview production build
```

## Routes

- `/react/` - Home page
- `/react/health` - Health check

## License

LGPL-3.0
