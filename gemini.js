const express = require("express");
const path = require("path");
const multer = require("multer");
const { GoogleGenerativeAI } = require("@google/generative-ai");
const { promises: fs } = require("fs");
const dotenv = require("dotenv");
const cors = require("cors");

dotenv.config();

const app = express();
const port = process.env.PORT || 3000;

app.use(cors());
app.use(express.static(path.join(__dirname, "Modules")));

const storage = multer.memoryStorage();
const upload = multer({ storage: storage });

app.post("/generateContent", upload.single("image"), async (req, res) => {
    try {
        const { buffer } = req.file;
        const { prompt } = req.body; // Retrieve the prompt from the request body

        // Use the prompt in the content generation process
        const genAI = new GoogleGenerativeAI(process.env.API_KEY);
        const generationConfig = {
            temperature: 0.4,
            topP: 1,
            topK: 32,
            maxOutputTokens: 4096,
        };
        const model = genAI.getGenerativeModel({
            model: "gemini-pro-vision",
            generationConfig,
        });

        const parts = [
            { text: prompt + "\n" }, // Use the user-entered prompt
            {
                inlineData: {
                    mimeType: "image/jpeg",
                    data: buffer.toString("base64"),
                },
            },
        ];

        const result = await model.generateContent({
            contents: [{ role: "user", parts }],
        });
        const response = await result.response;

        res.json({ success: true, description: response.text() });
    } catch (error) {
        console.error("Error generating content:", error);
        res.json({ success: false, error: "Error generating content" });
    }
});

app.get("/config", (req, res) => {
    try {
        const apiKey = process.env.API_KEY;
        if (!apiKey) {
            throw new Error("API_KEY is not set.");
        }

        res.json({ success: true, message: "API success" });
    } catch (error) {
        console.error("Error in /config endpoint:", error);
        res.status(500).json({ error: error.message });
    }
});

app.get("/generateContent/:prompt", async (req, res) => {
    try {
        const prompt = req.params.prompt;

        const apiKey = process.env.API_KEY;
        if (!apiKey) {
            throw new Error("API_KEY is not set.");
        }

        const genAI = new GoogleGenerativeAI(apiKey);
        const model = genAI.getGenerativeModel({ model: "gemini-pro" });

        const result = await model.generateContent({
            contents: [{ role: "user", parts: [{ text: prompt }] }],
        });

        const response = await result.response;
        const text = await response.text();
        res.send(text);
    } catch (error) {
        console.error("Error generating content:", error);
        res.status(500).send("Error generating content");
    }
});

app.listen(port, () => {
    console.log(`Server is running at http://localhost:${port}`);
});
