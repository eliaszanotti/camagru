import express from "express";
import authRoutes from "./authRoutes.mjs";

const router = express.Router();

router.get("/", (req, res) => {
	res.render("index", { title: "Podium" });
});

router.use("/auth", authRoutes);

export default router;
