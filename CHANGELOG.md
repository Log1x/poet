## v1.0.8 (04/30/20)

- feat(poet): Add block category support
- fix(post): Fix post type registration when using multiple string keys.
- fix(taxonomy): Fix taxonomy registration when using multiple string keys.
- enhance(poet): Make the post type, taxonomy, and block registration loop more performant.
- chore(deps): Bump dependencies
- chore(docs): Add block category examples to README

## v1.0.7 (04/01/20)

- enhance(posttype): Allow unregistering existing post types by setting it to `false`
- enhance(taxonomy): Allow unregistering existing taxonomies by setting it to `false`
- chore(docs): Update docs with unregister examples

## v1.0.6 (03/31/20)

- fix(post): Actually fix post type registration

## v1.0.5 (03/31/20)

- fix(post): Fix post type registration

## v1.0.4 (03/31/20)

- fix(block): Fix block registration

## v1.0.3 (03/28/20)

### Bug fixes

- fix(block): Fix namespace TextDomain fallback returning empty
- fix(post): Fix registering posts with only a string
- fix(taxonomy): Fix registering taxonomies with only a string

### Enhancements

- enhance(poet): Clean up and seperate post type and taxonomy registration methods
- enhance(poet): Use `Arr::get()` for handling array getters with fallback values
- chore(config): Lowercase instances of "block"

## v1.0.2 (03/25/20)

### Enhancements

- feat(block): Use current theme text domain if no namespace is given on registered block.
- enhance(poet): Improve and optimize the Poet class.
- enhance(poet): Optimize and split the `register()` method.
- enhance(poet): Improve and merge post type and taxonomy registration implementations.
- enhance(block): Improve the block registration implementation.
- enhance(block): Move content conditional to standalone `isEmpty()` method.
- chore(poet): Bump minimum PHP to 7.2.5
- chore(poet): Make method docblocks more verbose.
- chore(config): Improve english in docblock.
- chore(docs): Improve README.md documentation
- chore(license): Bump license to 2020.

## v1.0.1 (03/24/20)

### Enhancements

- feat(blocks): Add support for rendering registered Blocks with Blade.

## v1.0.0 (11/10/19)

- Initial release
