import express from "express";
import multer from "multer";
import path from "path";
import Post from "../../models/Post.mjs";
import { errors } from "../../utils/errors.mjs";
import { authMiddleware } from "../../middleware/authMiddleware.mjs";
import { fileURLToPath } from "url";
import sharp from "sharp";

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const storage = multer.diskStorage({
	destination: function (req, file, cb) {
		cb(null, "uploads/");
	},
	filename: function (req, file, cb) {
		const uniqueSuffix = Date.now() + "-" + Math.round(Math.random() * 1e9);
		cb(null, uniqueSuffix + path.extname(file.originalname));
	},
});

const upload = multer({ storage: storage });
const router = express.Router();

router.get("/add", authMiddleware, (req, res) => {
	res.render("galleryAdd");
});

router.post(
	"/upload",
	authMiddleware,
	upload.single("imageFile"),
	async (req, res) => {
		try {
			const userId = req.user.id;
			const imageFile = req.file;

			if (!imageFile) {
				return res.status(400).json(errors.IMAGE_REQUIRED);
			}

			const originaImagePath = path.join(
				__dirname,
				"../../" + imageFile.path
			);
			const processedImagePath = path.join(
				__dirname,
				"../../uploads",
				`processed-${Date.now()}-${Math.round(Math.random() * 1e9)}.png`
			);

			await sharp(originaImagePath)
				.resize(1500, 1500, {
					fit: "cover",
					position: "center",
				})
				.toFile(processedImagePath);

			const processedImageUrl = `/uploads/${path.basename(
				processedImagePath
			)}`;

			const newPost = new Post({
				userId: userId,
				originalImageUrl: processedImageUrl,
				imageUrl: processedImageUrl,
				createdAt: new Date(),
			});

			await newPost.save();
			res.redirect("/gallery");
		} catch (error) {
			console.error(error);
			res.status(500).json(errors.PUBLISHING_POST);
		}
	}
);

router.post("/add-emoji/:postId/:emojiId", authMiddleware, async (req, res) => {
	const { postId, emojiId } = req.params;

	try {
		const post = await Post.findById(postId);
		if (!post) {
			return res.status(404).json({ message: "Post not found" });
		}

		const originalImagePath = path.join(
			__dirname,
			"../../" + post.originalImageUrl
		);
		const emojiPath = path.join(
			__dirname,
			"../../public/emoji/emoji" + emojiId + ".png"
		);

		const image = sharp(originalImagePath);
		const emoji = await sharp(emojiPath).resize(300, 300).toBuffer();

		const { height } = await image.metadata();
		const x = 50;
		const y = height - 300 - 50;

		const newImagePath = path.join(
			__dirname,
			"../../uploads",
			`post-${Date.now()}-${Math.round(Math.random() * 1e9)}.png`
		);

		await image
			.composite([{ input: emoji, top: y, left: x }])
			.toFile(newImagePath);

		post.imageUrl = `/uploads/${path.basename(newImagePath)}`;
		await post.save();
		res.redirect("/gallery/edit/" + postId);
	} catch (error) {
		console.error(error);
		res.status(500).json(errors.PUBLISHING_POST);
	}
});

router.get("/webcam", authMiddleware, (req, res) => {
	res.render("galleryWebcam");
});

router.get("/import", authMiddleware, (req, res) => {
	res.render("galleryImport");
});

router.get("/edit/:postId", authMiddleware, async (req, res) => {
	const post = await Post.findById(req.params.postId);
	res.render("galleryEdit", { post });
});

router.get("/", authMiddleware, async (req, res) => {
	try {
		const posts = await Post.find({ userId: req.user.id })
			.populate("userId")
			.sort({ createdAt: -1 });
		res.render("gallery", {
			posts: posts,
		});
	} catch (error) {
		res.status(500).json(errors.GETTING_POSTS);
	}
});

router.post("/clear-image/:postId", authMiddleware, async (req, res) => {
	try {
		const post = await Post.findById(req.params.postId);
		if (!post) {
			return res.status(404).json({ message: "Post not found" });
		}

		post.imageUrl = post.originalImageUrl;
		await post.save();
		res.redirect("/gallery/edit/" + req.params.postId);
	} catch (error) {
		console.error(error);
		res.status(500).json(errors.PUBLISHING_POST);
	}
});

export default router;
