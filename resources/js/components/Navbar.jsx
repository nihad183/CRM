import React from "react";
import { Dock, DockItem } from "./DockAnimation";
import {
  Home,
  FolderPlus,
  FileUser,
  FileText,
  User,
} from "lucide-react";

export default function Navbar() {
  const currentPath = window.location.pathname;

  return (
    <div className="fixed top-8 left-0 right-0 flex justify-center z-50">
      <Dock>
        <DockItem
          label="Home"
          isActive={currentPath === "/" || currentPath === "/dashboard"}
          href="/dashboard"
        >
          <Home
            color={currentPath === "/" || currentPath === "/dashboard" ? "#2563eb" : "#000000"}
            size={20}
          />
        </DockItem>
        <DockItem
          label="New dossier"
          isActive={currentPath === "/new-dossier"}
          href="/new-dossier"
        >
          <FolderPlus color={currentPath === "/new-dossier" ? "#2563eb" : "#000000"} size={20} />
        </DockItem>
        <DockItem
          label="Fiche Client"
          isActive={currentPath === "/fiche-client"}
          href="/fiche-client"
        >
          <FileUser color={currentPath === "/fiche-client" ? "#2563eb" : "#000000"} size={20} />
        </DockItem>
        <DockItem
          label="Fiche Prospect"
          isActive={currentPath === "/fiche-propose"}
          href="/fiche-propose"
        >
          <FileText color={currentPath === "/fiche-propose" ? "#2563eb" : "#000000"} size={20} />
        </DockItem>
        <DockItem label="Profil" isActive={currentPath === "/profil"} href="/profil">
          <User color={currentPath === "/profil" ? "#2563eb" : "#000000"} size={20} />
        </DockItem>
      </Dock>
    </div>
  );
}
