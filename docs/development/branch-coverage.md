# Branch Coverage

Use Xdebug branch coverage when auditing integration selection and version handling:

```shell
nix develop .#xdebug
composer coverage:branch
```

The report is written beneath `coverage/branch`. Branch coverage is a local diagnostic and is not a CI gate.

The in-process Larastan adapter tests remain part of ordinary PHPUnit and mutation testing, but the branch profile
excludes them. Xdebug 3.5 path coverage crashes while instrumenting PHPStan's `RuleTestCase` analysis, and the
type-inference harness consumes more than 2 GiB when path enumeration is enabled. The branch report therefore audits
integration selection and version handling; the isolated consumer matrix continues to exercise the adapters against real
packages.
