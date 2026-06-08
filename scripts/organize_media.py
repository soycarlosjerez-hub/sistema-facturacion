import os
import shutil
import tkinter as tk
from tkinter import filedialog, ttk, messagebox

# =========================
# SELECCIONAR DIRECTORIO
# =========================
root = tk.Tk()
root.withdraw()

ruta_principal = filedialog.askdirectory(
    title="Selecciona la carpeta principal"
)

if not ruta_principal:
    print("No se seleccionó ninguna carpeta.")
    exit()

# =========================
# BUSCAR ARCHIVOS
# =========================
extensiones = ['.mp3', '.wav']
archivos_encontrados = []

for root_dir, dirs, files in os.walk(ruta_principal):
    for file in files:
        if os.path.splitext(file)[1].lower() in extensiones:
            archivos_encontrados.append(os.path.join(root_dir, file))

total_archivos = len(archivos_encontrados)

if total_archivos == 0:
    messagebox.showinfo("Sin archivos", "No se encontraron MP3 o WAV.")
    exit()

# =========================
# VENTANA PROGRESO
# =========================
ventana = tk.Tk()
ventana.title("Organizando archivos")
ventana.geometry("500x180")
ventana.resizable(False, False)

label_estado = tk.Label(
    ventana,
    text="Iniciando...",
    font=("Arial", 11)
)
label_estado.pack(pady=10)

progress = ttk.Progressbar(
    ventana,
    orient="horizontal",
    length=450,
    mode="determinate"
)
progress.pack(pady=10)

label_porcentaje = tk.Label(
    ventana,
    text="0%",
    font=("Arial", 10, "bold")
)
label_porcentaje.pack()

label_actual = tk.Label(
    ventana,
    text="",
    wraplength=450,
    justify="left"
)
label_actual.pack(pady=10)

ventana.update()

# =========================
# MOVER ARCHIVOS
# =========================
procesados = 0

for origen in archivos_encontrados:

    archivo = os.path.basename(origen)
    destino = os.path.join(ruta_principal, archivo)

    # Evitar mover si ya está en raíz
    if origen != destino:

        # Renombrar si existe
        if os.path.exists(destino):
            nombre, ext = os.path.splitext(archivo)
            contador = 1

            while os.path.exists(destino):
                nuevo_nombre = f"{nombre}_{contador}{ext}"
                destino = os.path.join(ruta_principal, nuevo_nombre)
                contador += 1

        try:
            shutil.move(origen, destino)
        except Exception as e:
            print(f"Error moviendo {archivo}: {e}")

    procesados += 1

    porcentaje = int((procesados / total_archivos) * 100)

    progress["value"] = porcentaje

    label_estado.config(
        text=f"Procesando archivos ({procesados}/{total_archivos})"
    )

    label_porcentaje.config(
        text=f"{porcentaje}%"
    )

    label_actual.config(
        text=f"Archivo actual:\n{archivo}"
    )

    ventana.update_idletasks()

# =========================
# ELIMINAR CARPETAS VACÍAS
# =========================
for root_dir, dirs, files in os.walk(ruta_principal, topdown=False):
    for dir_name in dirs:
        carpeta = os.path.join(root_dir, dir_name)

        try:
            if not os.listdir(carpeta):
                os.rmdir(carpeta)
        except:
            pass

# =========================
# FINALIZAR
# =========================
progress["value"] = 100

label_estado.config(text="Proceso completado")
label_porcentaje.config(text="100%")
label_actual.config(text="Todos los archivos fueron organizados.")

messagebox.showinfo(
    "Completado",
    "Todos los MP3 y WAV fueron movidos correctamente."
)

ventana.mainloop()