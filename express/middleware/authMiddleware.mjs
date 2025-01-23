import jwt from "jsonwebtoken";

export const authMiddleware = (req, res, next) => {
	const token = req.cookies.token;

	if (!token) {
		return res.redirect("/auth/login?next=" + encodeURIComponent(req.originalUrl));
	}

	try {
		const decoded = jwt.verify(token, process.env.JWT_SECRET);
		req.user = decoded;
		next();
	} catch (error) {
		return res.status(401).json({ message: "Token invalide" });
	}
};
