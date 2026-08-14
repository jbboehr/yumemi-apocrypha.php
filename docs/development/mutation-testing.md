# Mutation Testing

Mutation testing is an occasional audit of loader decisions and version-policy branches, not a substitute for running
consumer projects against real upstream packages.

Mutation testing is an explicit Nix package used by exhaustive CI, not a normal `nix flake check` check. Run the same
target locally with:

```shell
nix build -L .#mutation
```

For an interactive investigation, enter the development shell, install dependencies, and run:

```shell
composer infection
```

Review surviving mutants for missing behavior assertions. Do not weaken version rejection, integration selection, or
consumer diagnostics merely to improve the score.
