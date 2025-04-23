const express = require('express');
const router = express.Router();
const Post = require('../models/Post');

router.post('/', async (req, res) => {
    const { username, content } = req.body;
    const newPost = new Post({ username, content });
    await newPost.save();
    res.json(newPost);
});

router.get('/', async (req, res) => {
    const posts = await Post.find().sort({ date: -1 });
    res.json(posts);
});

module.exports = router;
