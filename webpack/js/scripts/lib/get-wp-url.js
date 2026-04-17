const { execSync } = require("child_process");
const path = require("path");
const fs = require("fs");
require("dotenv").config(); // permite fallback via .env

function findWpLoad(startPath) {
    let current = startPath;
    while (current !== path.parse(current).root) {
        const wpLoad = path.join(current, "wp-load.php");
        if (fs.existsSync(wpLoad)) return wpLoad;
        current = path.dirname(current);
    }
    return null;
}

function getWPUrl() {
    try {
        const wpLoadPath = findWpLoad(__dirname);
        if (wpLoadPath) {
            const command = `php -r "include '${wpLoadPath.replace(
                /\\/g,
                "/"
            )}'; echo get_site_url();"`;
            const output = execSync(command, { encoding: "utf8" }).trim();
            if (output) {
                console.log(`🌐 Detectado domínio WordPress: ${output}`);
                return output;
            }
        }
    } catch (err) {
        console.log("⚠️  Falha ao executar o PHP CLI.");
    }

    // 🔁 Fallback via .env ou localhost
    const fallback = process.env.WP_URL || "http://localhost";
    console.log(`🔁 Usando domínio do fallback: ${fallback}`);
    return fallback;
}

module.exports = getWPUrl;
