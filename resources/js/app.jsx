//
import React from "react";
import { createRoot } from "react-dom/client";
import Navbar from "./components/Navbar";

const el = document.getElementById("navbar");

if (el) {
  createRoot(el).render(<Navbar />);
}