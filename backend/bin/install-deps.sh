#!/usr/bin/env bash
set -euo pipefail

script_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
backend_dir="$(cd "${script_dir}/.." && pwd)"

docker run --rm \
  -v "${backend_dir}:/app" \
  -w /app \
  composer:2 \
  install --no-dev --prefer-dist --no-interaction --no-progress
