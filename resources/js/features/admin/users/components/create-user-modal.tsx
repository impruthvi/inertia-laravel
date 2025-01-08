"use client";

import { ResponsiveModal } from "@/Components/ResponsiveModal";
import { useCreateUserModal } from "./hooks/use-create-user-modal";
import { CreateUserForm } from "./create-user-form";

// import { CreateProjectForm } from "./create-project-form";

export const CreateUserModal = () => {
  const { isOpen, setIsOpen, close } = useCreateUserModal();

  return (
    <ResponsiveModal open={isOpen} onOpenChange={setIsOpen}>
      <CreateUserForm onCancel={close} />
    </ResponsiveModal>
  );
};
