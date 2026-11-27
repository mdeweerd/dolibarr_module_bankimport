<?php
/* Copyright (C) 2026 MDW <mdeweerd@users.noreply.github.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file    .ci/scripts/smoke-router.php
 * \brief   Router of the smoke server (.ci/scripts/smoke-pages.sh)
 *
 * The built-in server serves every request from the document root: it
 * does not work from the directory of the requested script the way
 * Apache does, and the module pages include main.inc.php through
 * paths relative to their own directory ("../../main.inc.php" and
 * friends). Move to the directory of the requested script here, then
 * hand the request back to the server unchanged.
 */

$uri = isset($_SERVER['REQUEST_URI']) ? explode('?', $_SERVER['REQUEST_URI'], 2)[0] : '';
$file = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . rawurldecode($uri);
if (is_file($file)) {
	chdir(dirname($file));
}
return false;
