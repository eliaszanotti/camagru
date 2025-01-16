export const passwordValidation = (password) => {
	const passwordRegex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*]).{8,}$/;
	return passwordRegex.test(password);
};
