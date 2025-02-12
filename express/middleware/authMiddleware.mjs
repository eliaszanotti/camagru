import jwt from "jsonwebtoken";

export const authMiddleware = (req, res, next) => {
	const token = req.cookies.token;

	if (!token) {
		if (req.xhr || req.headers["x-requested-with"] === "XMLHttpRequest") {
			return res.status(401).json({ message: "Unauthorized" });
		}
		return res.redirect(
			"/auth/login?next=" + encodeURIComponent(req.originalUrl)
		);
	}

	try {
		const decoded = jwt.verify(token, process.env.JWT_SECRET);
		req.user = decoded;
		next();
	} catch (error) {
		return res.status(401).json({ message: "Invalid token" });
	}
};
