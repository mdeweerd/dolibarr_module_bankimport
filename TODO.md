# TODO

Steps in progress only - completed work lives in the git history and in
the action log. Anything not yet actionable belongs to PLAN.md or to an
issue, not here.

- [ ] Run the analyse workflow on this branch (Actions - analyse - Run
      workflow): it generates phpstan-baseline.neon and .phan/baseline.txt
      and fails once on purpose.
- [ ] Download the two baseline artifacts and commit them on the branch.
- [ ] Run the lint and test workflows on the branch, fix what they
      reveal, then open the pull request to main (PLAN.md, Phase 0).
