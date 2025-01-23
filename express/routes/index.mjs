import express from "express";
import authRoutes from "./authRoutes.mjs";
import { authMiddleware } from "../middleware/authMiddleware.mjs";

const router = express.Router();

router.get("/", (req, res) => {
	res.render("index", { title: "Podium" });
});

router.get("/profil", authMiddleware, (req, res) => {
	res.render("profil", { user: req.user });
});

router.use("/auth", authRoutes);

export default router;
