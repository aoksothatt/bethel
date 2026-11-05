import fs from 'node:fs'
import path from 'node:path'

const srcDir = path.resolve('dist')
const destDir = path.resolve('backend/public')

if (!fs.existsSync(srcDir)) {
  console.error('dist folder does not exist. Run npm run build first.')
  process.exit(1)
}

// Copy dist into backend/public recursively
fs.cpSync(srcDir, destDir, { recursive: true, force: true })
console.log('Successfully deployed built frontend to backend/public!')
