import express from "express";
// import authRoutes from "./authRoutes.mjs";

const router = express.Router();

// router.use("/auth", authRoutes);

router.get("/", (req, res) => {
	res.render("index", { title: "Podium" });
});

export default router;
