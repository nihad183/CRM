import {
  motion,
  useMotionValue,
  useSpring,
  useTransform,
} from "framer-motion";
import { createContext, useContext, useRef, useState } from "react";

const DockContext = createContext();

export function Dock({ children }) {
  const mouseX = useMotionValue(Infinity);

  return (
    <div
      onMouseMove={(e) => mouseX.set(e.pageX)}
      onMouseLeave={() => mouseX.set(Infinity)}
      className="flex gap-3 bg-gray-100 px-3 py-2 rounded-full"
    >
      <DockContext.Provider value={{ mouseX }}>
        {children}
      </DockContext.Provider>
    </div>
  );
}

export function DockItem({ children, label, isActive = false, href = "#" }) {
  const ref = useRef(null);
  const { mouseX } = useContext(DockContext);
  const [isHovered, setIsHovered] = useState(false);

  const distance = 100;

  const mouseDistance = useTransform(mouseX, (val) => {
    const rect = ref.current?.getBoundingClientRect();
    if (!rect) return 0;
    return val - (rect.x + rect.width / 2);
  });

  const width = useSpring(
    useTransform(mouseDistance, [-distance, 0, distance], [28, 58, 28]),
    { stiffness: 220, damping: 18 }
  );

  return (
    <div className="relative flex items-center justify-center">
      {label && (
        <motion.div
          initial={{ opacity: 0, y: 8, scale: 0.92 }}
          animate={
            isHovered
              ? { opacity: 1, y: 0, scale: 1 }
              : { opacity: 0, y: 8, scale: 0.92 }
          }
          transition={{ duration: 0.18, ease: "easeOut" }}
          className="pointer-events-none absolute -top-11 whitespace-nowrap rounded-md bg-white/95 px-2 py-1 text-xs font-medium text-black shadow-md"
        >
          {label}
        </motion.div>
      )}

      <motion.a
        ref={ref}
        href={href}
        style={{ width, height: width }}
        onHoverStart={() => setIsHovered(true)}
        onHoverEnd={() => setIsHovered(false)}
        className={`flex items-center justify-center rounded-full no-underline ${
          isActive ? "bg-blue-100 ring-2 ring-blue-300" : "bg-white"
        }`}
      >
        {children}
      </motion.a>
    </div>
  );
}
