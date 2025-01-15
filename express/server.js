const express = require("express");
const path = require("path");

const app = express();
const PORT = process.env.PORT || 3000;

app.set("views", path.join(__dirname, "views"));
app.set("view engine", "ejs");

app.use(express.static(path.join(__dirname, "public")));

app.get("/", (req, res) => {
	res.render("index", { title: "Accueil" });
});

app.get("/register", (req, res) => {
	res.render("register", { title: "Créer un Compte" });
});

app.listen(PORT, () => {
	console.log(`Server is running on http://localhost:${PORT}`);
});
