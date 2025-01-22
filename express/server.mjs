import express from "express";
import path from "path";
import mongoose from "mongoose";
import routes from "./routes/index.mjs";

const __dirname = path.dirname(new URL(import.meta.url).pathname);

const app = express();
const PORT = process.env.PORT || 3000;

const mongoURI = process.env.MONGO_URI;

mongoose
	.connect(mongoURI, { useNewUrlParser: true, useUnifiedTopology: true })
	.then(() => console.log("Connecté à MongoDB"))
	.catch((err) => console.error("Erreur de connexion à MongoDB:", err));

app.set("views", path.join(__dirname, "views"));
app.set("view engine", "ejs");

app.use(express.static(path.join(__dirname, "public")));
app.use(express.urlencoded({ extended: true }));

app.use("/", routes);

app.listen(PORT, () => {
	console.log(`Server is running on http://localhost:${PORT}`);
});
