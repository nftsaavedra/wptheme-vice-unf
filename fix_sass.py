import os
import glob

# Buscar todos los style.scss en los bloques de src
base_path = r"c:\Users\UPIC\Local Sites\vpindev\app\public\wp-content\themes\vpinunf\src\blocks"
files = glob.glob(os.path.join(base_path, "**", "style.scss"), recursive=True)

count = 0
for f in files:
    try:
        with open(f, 'r', encoding='utf-8') as file:
            content = file.read()
            
        if '@import "../../scss/base/mixins";' in content:
            content = content.replace('@import "../../scss/base/mixins";', '@use "../../scss/base/mixins" as *;')
            with open(f, 'w', encoding='utf-8') as file:
                file.write(content)
            count += 1
    except Exception as e:
        print(f"Error {f}: {e}")

print(f"Se actualizaron {count} archivos.")
