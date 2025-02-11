function createSinglePostHtml(post) {
	const singlePostHtml = `
			<div class="flex flex-col gap-10">
				<div class="flex gap-2 items-center justify-between">
					<h1 class="font-bold">${post.user.username}</h1>
					<p class="text-sm text-gray-500">${new Date(post.createdAt).toLocaleDateString(
						"fr-FR",
						{
							year: "numeric",
							month: "long",
							day: "numeric",
							hour: "numeric",
							minute: "numeric",
							hour12: false,
						}
					)}</p>
				</div>
				<img src="${
					post.imageUrl
				}" alt="Post Image" class="w-full aspect-square object-cover rounded-field" />
			</div>
		`;
	return singlePostHtml;
}

function createVoteScoreHtml(posts, index) {
	if (posts[0].votes > posts[1].votes) {
		return index === 0 ? "text-success" : "text-error";
	} else if (posts[0].votes < posts[1].votes) {
		return index === 0 ? "text-error" : "text-success";
	} else {
		return "text-gray-500";
	}
}

function calculateProgressWidth(posts, index) {
	const totalVotes = posts[0].votes + posts[1].votes;
	if (totalVotes === 0) return 0;
	if (index === 0) {
		return (posts[0].votes / totalVotes) * 100;
	} else {
		return (posts[1].votes / totalVotes) * 100;
	}
}

function createProgressBarHtml(posts) {
	const progressBarHtml = `
		<div class="grid grid-cols-2 w-2/5 bg-base-300 rounded-full overflow-hidden">
			<div class="flex justify-self-end h-4 bg-gray-500" style="width: ${calculateProgressWidth(
				posts,
				0
			)}%"></div>
			<div class="h-4 bg-gray-500" style="width: ${calculateProgressWidth(
				posts,
				1
			)}%"></div>
		</div>
	`;
	return progressBarHtml;
}

function createDualPost(data) {
	let postsHTML = "";
	for (const posts of data) {
		postsHTML += `
			<div class="grid grid-cols-2 bg-base-200 border border-base-300 p-10 gap-10 rounded-box">
				${createSinglePostHtml(posts[0])}
				${createSinglePostHtml(posts[1])}
				<div class="col-span-full flex justify-between items-center gap-10">
					<p class="font-bold text-xl ${createVoteScoreHtml(posts, 0)}">${
			posts[0].votes
		}</p>
					${createProgressBarHtml(posts)}
					<p class="font-bold text-xl ${createVoteScoreHtml(posts, 1)}">${
			posts[1].votes
		}</p>
				</div>
			</div>
		`;
	}
	return postsHTML;
}

function fetchPosts(count) {
	fetch(`/post/get-dual?count=${count}`)
		.then((response) => response.json())
		.then((data) => {
			const posts = document.getElementById("posts");
			posts.innerHTML += createDualPost(data);
		})
		.catch((error) => {
			console.error("Error fetching posts:", error);
		});
}
