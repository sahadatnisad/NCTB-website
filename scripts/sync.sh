#!/usr/bin/env bash
#
# sync.sh — one-command GitHub sync for the NCTB AI Learning Hub.
#
# Purpose: let any AI, on any device, save its work to GitHub so the next
# session (any AI, any machine) can `git pull` and continue. Run this at the
# END of a phase, after BUILD_STATE.md is updated.
#
# Usage:
#   bash scripts/sync.sh "Phase 3 complete: curriculum CMS"
#   bash scripts/sync.sh                # uses a default message
#
# It will: pull (rebase) latest → stage all → commit → push to origin/main.
# Secrets and generated files stay out via .gitignore.

set -euo pipefail

# Always operate from the repository root, regardless of where it is called.
REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$REPO_ROOT"

BRANCH="$(git rev-parse --abbrev-ref HEAD 2>/dev/null || echo main)"
MSG="${1:-"chore: sync build progress ($(date +%Y-%m-%d\ %H:%M))"}"

echo "==> Repo:   $REPO_ROOT"
echo "==> Branch: $BRANCH"

# 1) Commit local work FIRST so the tree is clean for a rebase pull.
git add -A
if git diff --cached --quiet; then
	echo "==> Nothing new to commit — working tree already clean."
else
	git commit -m "$MSG"
	echo "==> Committed: $MSG"
fi

# 2) Get the latest work from other devices/AIs (rebase to keep history linear).
echo "==> Pulling latest from origin/$BRANCH ..."
git pull --rebase origin "$BRANCH" || {
	echo "!! Pull failed (conflict). Resolve conflicts, run 'git rebase --continue', then re-run." >&2
	exit 1
}

# 3) Push to GitHub.
echo "==> Pushing to origin/$BRANCH ..."
git push origin "$BRANCH"

echo "==> Done. Work is synced to GitHub. Other devices can now 'git pull'."
