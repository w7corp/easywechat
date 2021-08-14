# EasyWeChat Website

The EasyWeChat docs website uses [Vue Press](https://vuepress.vuejs.org),
a minimalistic [Vue](https://vuejs.org) powered static site generator.

## Directory structure

```
docs/
├── .vuepress/            # https://vuepress.vuejs.org/guide/directory-structure.html
│
├── master/
│   ├── guides/
│   │   └── auth.md       # https://mysite.com/master/guides/auth.html
│   ├── the-basics/
│   │   └── fields.md     # https://mysite.com/master/the-basics/fields.html
│   │
│   └── sidebar.js        # versioned sidebar for this version
│
├── 2/
│   └── ...               # same structure as "docs/master/"
|
├── 3/
│   └── ...               # same structure as "docs/master/"
│
├── pages/
│   └── ...               # Not versioned, it remains the same for all docs versions
│
├── package.json          # vuepress dependencies
└── INDEX.md              # the beautiful home page
```

## Development

Make sure you have:

- Node 8+
- Yarn

Then, start Vue Press in development mode (with hot reloading).

    cd docs/
    yarn
    yarn start

> Keep an eye on the console when editing pages.
> If an error occurs, it might be necessary to restart the compilation process.

If you use Docker you can start up the environment (including docs) by running:

    make setup
    make node

Finally, navigate to http://localhost:8081

## Files

### Creating new files

- Place the new file into `master`, e.g. `docs/master/new/feature.md`
- Include the reference to the new file into `docs/master/sidebar.js`

### Linking files

Remember to include the `.md` extension.

```md
The [@paginate](directives.md#paginate) directive is great!
```

Always use relative paths according to folder structure.

```md
See [configuration](../getting-started/configuration.md) for more info.
```

## Versioning

Each subfolder in `docs/` will represent a documentation version,
except `docs/pages/` that will remain the same for all docs versions.

This ensures that the docs are always in sync with the released version of EasyWeChat.
Version specific changes are handled by keeping the docs for each version separate.

| Path                                 | Web route                                            |
| ------------------------------------ | ---------------------------------------------------- |
| `docs/master/guides/installation.md` | `https://mysite.com/master/guides/installation.html` |
| `docs/2/guides/installation.md`      | `https://mysite.com/2/guides/installation.html`      |
| `docs/pages/users.md`                | `https://mysite.com/pages/users.html`                |

### Updating existing versions

When you improve the docs, consider if the change you are making applies to
multiple versions of EasyWeChat.

Just change the relevant parts of each separate docs folder and commit it all
in a single PR.

### Tagging a new version

After you finished your work on `docs/master`, copy the updated docs
into the directory of the current major version by running:

    yarn release

When releasing a new major version, update the `release` script in `package.json` first.

