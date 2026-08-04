# Branch Coverage

Use Xdebug branch coverage when auditing integration selection and version handling:

```shell
nix develop .#xdebug
composer coverage:branch
```

The report is written beneath `coverage/branch`. Branch coverage is a local diagnostic and is not a CI gate.
