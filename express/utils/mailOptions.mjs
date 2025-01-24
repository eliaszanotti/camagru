export const verifyMailOptions = (user) => {
	return {
		from: process.env.GOOGLE_USER,
		to: user.email,
		subject: "Verification of your email",
		html: `
		<h1>Hi ${user.username}, welcome to Podium !</h1>
		<p>Please click on this link to verify your email: 
			<a href="${process.env.SERVER_URL}/auth/verify-email/${user.emailVerificationToken}">
			Verify my email</a>
		</p>`,
	};
};

export const resetPasswordMailOptions = (user) => {
	return {
		from: process.env.GOOGLE_USER,
		to: user.email,
		subject: "Reset your password",
		html: `
		<h1>Hi ${user.username},</h1>
		<p>Please click on this link to reset your password: 
			<a href="${process.env.SERVER_URL}/auth/reset-password/${user.resetPasswordToken}">
			Reset my password</a>
		</p>
		<p>This link will expire in 10 minutes.</p>`,
	};
};