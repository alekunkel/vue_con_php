const express = require('express');
const mysql = require('mysql2/promise');
const cors = require('cors');

const app = express();
const PORT = 3000;

app.use(express.json());
app.use(cors());

// --- Configuración de la base de datos ---
// ¡IMPORTANTE! Reemplaza los valores con los de tu base de datos
const dbConfig = {
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'escuela'
};

// ----------------------------------------------------
// Endpoint para OBTENER todos los estudiantes
// ----------------------------------------------------
app.get('/api/obtener-estudiantes', async (req, res) => {
    try {
        const connection = await mysql.createConnection(dbConfig);
        const [rows] = await connection.execute('SELECT id, nombre, curso, materia, aprobado, desaprobado FROM estudiantes');
        await connection.end();

        const estudiantesProcesados = rows.map(fila => {
            let condicion = 'sinmarcar';
            if (fila.aprobado === 1) {
                condicion = 'aprobado';
            } else if (fila.desaprobado === 1) {
                condicion = 'desaprobado';
            }

            return {
                id: fila.id,
                nombre_apellido: fila.nombre,
                curso: fila.curso,
                materia: fila.materia,
                condicion: condicion
            };
        });

        res.status(200).json(estudiantesProcesados);
    } catch (error) {
        console.error('Error al obtener estudiantes:', error);
        res.status(500).json({ error: 'Error interno del servidor.' });
    }
});

// ----------------------------------------------------
// Endpoint para GUARDAR un estudiante
// ----------------------------------------------------
app.post('/api/guardar-estudiante', async (req, res) => {
    try {
        const { nombre_apellido, curso, materia, condicion } = req.body;

        if (!nombre_apellido || !curso || !materia) {
            return res.status(400).json({ error: "Todos los campos son obligatorios." });
        }

        let aprobado = 0;
        let desaprobado = 0;

        if (condicion === 'aprobado') {
            aprobado = 1;
        } else if (condicion === 'desaprobado') {
            desaprobado = 1;
        }

        const connection = await mysql.createConnection(dbConfig);
        const sql = "INSERT INTO estudiantes (nombre, curso, materia, aprobado, desaprobado) VALUES (?, ?, ?, ?, ?)";
        const [result] = await connection.execute(sql, [nombre_apellido, curso, materia, aprobado, desaprobado]);
        
        await connection.end();

        res.status(200).json({ mensaje: "Estudiante guardado con éxito!", id: result.insertId });
    } catch (error) {
        console.error('Error al guardar estudiante:', error);
        res.status(500).json({ error: "Error al guardar el estudiante: " + error.message });
    }
});

// ----------------------------------------------------
// Endpoint para ELIMINAR un estudiante
// ----------------------------------------------------
app.post('/api/eliminar-estudiante', async (req, res) => {
    try {
        const id = req.body.id;
        if (!id) {
            return res.status(400).json({ error: "ID del estudiante no proporcionado." });
        }
        const connection = await mysql.createConnection(dbConfig);
        const query = "DELETE FROM estudiantes WHERE id = ?";
        const [result] = await connection.execute(query, [id]);
        await connection.end();
        if (result.affectedRows === 0) {
            return res.status(404).json({ error: "Estudiante no encontrado." });
        }
        res.status(200).json({ mensaje: "Estudiante eliminado correctamente." });
    } catch (error) {
        console.error('Error al eliminar estudiante:', error);
        res.status(500).json({ error: "Error al eliminar el estudiante: " + error.message });
    }
});

// ----------------------------------------------------
// Iniciar el servidor
// ----------------------------------------------------
app.listen(PORT, () => {
    console.log(`API corriendo en http://localhost:${PORT}`);
});