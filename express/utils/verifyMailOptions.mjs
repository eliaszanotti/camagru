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
