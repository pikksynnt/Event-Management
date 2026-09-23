import React from "react";
import { requireEventManager } from "@/lib/auth";
import { Navbar } from "@/components/Navbar";

export const metadata = {
  title: "Dashboard Event Manager | Sistem Event Management",
  description: "Kelola dan tinjau pengajuan event dengan mudah dan cepat.",
};

export default async function ManagerDashboardLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  // Layer 2 Server-Side Authorization Check
  const session = await requireEventManager();

  return (
    <div className="min-h-screen bg-slate-50 text-slate-900 flex flex-col">
      <Navbar userName={session.name} />
      <main className="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {children}
      </main>
    </div>
  );
}
