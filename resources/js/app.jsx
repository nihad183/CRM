//
import React from "react";
import { createRoot } from "react-dom/client";
import Navbar from "./components/Navbar";

const el = document.getElementById("navbar");
const normalizedRole = (el?.dataset?.userRole || "employee").trim().toLowerCase();

if (el) {
  createRoot(el).render(<Navbar userRole={normalizedRole} />);
}
