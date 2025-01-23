import jwt from "jsonwebtoken";

export const globalMiddleware = (req, res, next) => {
	const token = req.cookies.token;

	res.locals.isLoggedIn = false;

	if (token) {
		try {
			const decoded = jwt.verify(token, process.env.JWT_SECRET);
			req.user = decoded;
			res.locals.isLoggedIn = true;
		} catch (error) {
			console.error("Token invalide:", error);
		}
	}

	next();
};
